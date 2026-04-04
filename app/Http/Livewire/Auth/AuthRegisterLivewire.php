<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use App\Models\UserProfile;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class AuthRegisterLivewire extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Admin gate
    public bool $isAdmin = false;

    // Table state
    public string $q = '';
    public string $statusFilter = '';
    public int $perPage = 10;
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    protected array $sortable = ['name','email','status','created_at'];

    // Modal state
    public bool $showModal = false;
    public ?int $editId = null;

    // Form fields (User)
    public string $name = '';
    public string $email = '';
    public string $password = '';      // only for create / when filled on edit
    public string $g_password = '';    // optional (if you use it)
    public $status = 1;  // 'active' | 'inactive'

    // Form fields (Profile)
    public string $phone = '';
    public string $country = '';
    public string $state = '';
    public string $city = '';
    public string $address = '';

    // Avatar upload
    public $avatarUpload = null; // Livewire temporary upload
    public ?string $currentAvatar = null; // stored path in s3

    protected $queryString = [
        'q' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'page' => ['except' => 1],
    ];

    public function mount(): void
    {
        $this->isAdmin = (int)auth()->user()->role === 1;
        abort_if(!$this->isAdmin, 403, 'Forbidden');
    }

    public function updatingQ()            { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingPerPage()      { $this->resetPage(); }

    public function sort(string $column): void
    {
        if (!in_array($column, $this->sortable, true)) return;
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    protected function rules(): array
    {
        $emailUnique = Rule::unique('users', 'email');
        if ($this->editId) {
            $emailUnique = $emailUnique->ignore($this->editId);
        }

        $passwordRules = ['nullable', 'string'];
        if (!$this->editId) {
            $passwordRules = ['required', 'string', 'min:8'];
        } elseif (filled($this->password)) {
            $passwordRules[] = 'min:8';
        }

        return [
            'name'        => ['required','string','max:120'],
            'email'       => ['required','email','max:190', $emailUnique],
            'password'    => $passwordRules,
            'g_password'  => ['nullable','string','max:190'],
            'status'      => ['required', Rule::in([1,0])],
            'phone'       => ['nullable','string','max:30'],
            'country'     => ['nullable','string','max:100'],
            'state'       => ['nullable','string','max:100'],
            'city'        => ['nullable','string','max:100'],
            'address'     => ['nullable','string','max:255'],
            'avatarUpload'=> ['nullable','image','mimes:jpeg,png,jpg,webp','max:2048'],
        ];
    }

    public function updatedAvatarUpload(): void
    {
        $this->validateOnly('avatarUpload');
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetForm();

        $u = User::with('profile')->where('role', 2)->findOrFail($id);
        $this->editId = $u->id;
        $this->name   = (string)$u->name;
        $this->email  = (string)$u->email;
        $this->status = (string)($u->status ?? 1);
        $this->g_password = (string)($u->g_password ?? '');

        $p = $u->profile;
        $this->phone   = (string)($p->phone ?? '');
        $this->country = (string)($p->country ?? '');
        $this->state   = (string)($p->state ?? '');
        $this->city    = (string)($p->city ?? '');
        $this->address = (string)($p->address ?? '');
        $this->currentAvatar = $p->avatar ?? null;

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                if ($this->editId) {
                    $user = User::where('role', 2)->lockForUpdate()->findOrFail($this->editId);
                } else {
                    $user = new User();
                    $user->role = 2; // register
                }

                $user->name   = $this->name;
                $user->email  = $this->email;
                $user->status = $this->status;
                $user->g_password = Hash::make('asdasdasd');

                if (filled($this->password)) {
                    $user->password = $this->password; // cast 'hashed' in model will hash
                }
                $user->save();

                // Ensure profile exists
                $profile = $user->profile()->firstOrNew([]);

                $profile->phone   = $this->phone ?: null;
                $profile->country = $this->country ?: null;
                $profile->state   = $this->state ?: null;
                $profile->city    = $this->city ?: null;
                $profile->address = $this->address ?: null;

                // Avatar upload & 1:1 crop to 400x400
                if ($this->avatarUpload) {
                    $path = $this->processAvatar($this->avatarUpload, $this->currentAvatar);
                    $profile->avatar = $path;
                    $this->currentAvatar = $path;
                }

                $profile->save();
            });
        } catch (\Throwable $e) {
            report($e);

            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Unable to save the register right now. Please try the avatar upload again.'),
            ]);

            return;
        }

        $this->avatarUpload = null;
        $this->showModal = false;
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => $this->editId ? __('Register has been updated successfully') : __('Register has been added successfully'),
        ]);
        $this->resetPage();
    }

    /**
     * Crop to 1:1 and save to public disk under /avatars.
     * Deletes old avatar if present.
     */
    // protected function processAvatar($uploaded, ?string $oldPath): string
    // {
    //     // Read, auto-rotate from EXIF, crop to 1:1 (400x400)
    //     $img = Image::read($uploaded->getRealPath())
    //         ->cover(400, 400);  // center-crop to square

    //     $filename = 'avatars/'.uniqid('av_').'.jpg';

    //     // Encode to JPEG and store on public disk
    //     Storage::disk('public')->put($filename, $img->toJpeg(85)->toString());

    //     // remove old file if any
    //     if ($oldPath && Storage::disk('public')->exists($oldPath)) {
    //         Storage::disk('public')->delete($oldPath);
    //     }

    //     return $filename;
    // }
    protected function processAvatar($uploaded, ?string $oldPath): string
    {
        // Split name -> first/last
        [$first, $last] = $this->splitFirstLast($this->name);
        $first = Str::slug($first ?: 'register');
        $last  = Str::slug($last ?: 'user');

        // microseconds (no dot)
        $usec = str_replace('.', '', sprintf('%.6f', microtime(true)));

        // avatar/<first>_<last>_<usec>.jpg
        $key = "avatar/{$first}_{$last}_{$usec}.jpg";

        // Crop -> encode jpeg
        $img = Image::read($uploaded->getRealPath())
            ->cover(400, 400)
            ->toJpeg(85)
            ->toString();

        try {
            $this->freshS3Disk(true)->put($key, $img, [
                'ContentType' => 'image/jpeg',
                'CacheControl' => 'public, max-age=31536000',
            ]);
        } catch (\Throwable $e) {
            Log::error('Register avatar upload failed.', [
                'userId' => auth()->id(),
                'registerId' => $this->editId,
                'avatar_key' => $key,
                'disk' => 's3',
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            throw new \RuntimeException('Avatar upload to S3 failed: '.$e->getMessage(), 0, $e);
        } finally {
            $this->freshS3Disk(false);
        }

        // Best-effort delete of old file; ignore network errors
        if ($oldPath) {
            try {
                if (Storage::disk('s3')->exists($oldPath)) {
                    Storage::disk('s3')->delete($oldPath);
                }
            } catch (\Throwable $e) {
                // Nothing
            }
        }

        return $key;
    }

    protected function freshS3Disk(bool $throw = false)
    {
        config([
            'filesystems.disks.s3.throw' => $throw,
            'filesystems.disks.s3.visibility' => 'private',
        ]);
        app('filesystem')->forgetDisk('s3');

        return Storage::disk('s3');
    }


    protected function splitFirstLast(string $full): array
    {
        $parts = array_values(array_filter(preg_split('/\s+/u', trim($full) ?: ''), fn($p) => $p !== ''));
        if (count($parts) === 0) return ['', ''];
        if (count($parts) === 1) return [$parts[0], $parts[0]];
        return [$parts[0], $parts[count($parts) - 1]];
    }

    public function toggleStatus(int $id): void
    {
        try {
            $u = User::where('role', 2)->findOrFail($id);
            $u->status = ($u->status === 1) ? 0 : 1;
            $u->save();
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Register Status updated successfully'),
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Something Went Wrong'),
            ]);
        }
    }

    public function deleteUser(int $id): void
    {
        $u = User::where('role', 2)->with('profile')->findOrFail($id);

        if ($u->profile && $u->profile->avatar && Storage::disk('s3')->exists($u->profile->avatar)) {
            Storage::disk('s3')->delete($u->profile->avatar);
        }

        optional($u->profile)->delete();
        $u->delete();

        try {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Register Has Been Deleted successfully'),
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Something Went Wrong'),
            ]);
        }

        $this->resetPage();
    }


    protected function resetForm(): void
    {
        $this->reset([
            'editId','name','email','password','status',
            'phone','country','state','city','address',
            'avatarUpload'
        ]);
        $this->status = 1;
        $this->currentAvatar = null;
    }

    protected function tableQuery()
    {
        $q = User::with('profile')->where('role', 2);

        if ($this->q !== '') {
            $term = '%'.str_replace(['%','_'], ['\%','\_'], trim($this->q)).'%';
            $q->where(function($w) use ($term) {
                $w->where('name', 'like', $term)
                  ->orWhere('email','like',$term)
                  ->orWhereHas('profile', fn($p) => $p->where('phone','like',$term));
            });
        }

        if ($this->statusFilter !== '') {
            $q->where('status', $this->statusFilter);
        }

        if (in_array($this->sortBy, $this->sortable, true)) {
            $q->orderBy($this->sortBy, $this->sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $q->orderBy('created_at', 'desc');
        }

        return $q;
    }

    public function render()
    {
        $rows = $this->tableQuery()->paginate($this->perPage);

        return view('components.auth.register-table', [
            'rows' => $rows,
        ]);
    }
}
