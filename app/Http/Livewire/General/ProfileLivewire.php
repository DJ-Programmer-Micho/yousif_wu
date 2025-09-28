<?php

namespace App\Http\Livewire\General;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

// models
use App\Models\Sender;
use App\Models\Receiver;
use App\Models\User;

class ProfileLivewire extends Component
{
    use WithFileUploads;

    public $user;

    // UI data
    public $roleLabel = 'User';
    public $joinedYear;
    public $joinedHuman;

    // Stats
    public $sendersExecutedCount = 0;
    public $sendersExecutedTotal = 0.0;
    public $receiversExecutedCount = 0;
    public $receiversExecutedTotal = 0.0;

    // Recent
    public $recentSenders = [];
    public $recentReceivers = [];

    // NEW: avatar modal + upload
    public bool $showAvatarModal = false;
    public $avatarUpload = null;

    // NEW: inline edit basics
    public bool $showEditBasics = false;
    public string $name = '';
    public ?string $phone = null;
    public ?string $address = null;

    protected function rules(): array
    {
        return [
            // avatar
            'avatarUpload' => ['nullable','image','mimes:jpeg,png,jpg,webp','max:2048'],
            // basics
            'name' => ['required','string','max:120'],
            'phone' => ['nullable','string','max:30'],
            'address' => ['nullable','string','max:255'],
        ];
    }

    public function mount()
    {
        $this->user = Auth::user();

        $this->roleLabel = auth()->user()->role == 1 ? 'Admin' : 'User';
        $this->joinedYear  = optional($this->user->created_at)->format('Y');
        $this->joinedHuman = optional($this->user->created_at)->diffForHumans();

        // preload basics
        $this->name = (string) ($this->user->name ?? '');
        $this->phone = (string) (optional($this->user->profile)->phone ?? '');
        $this->address = (string) (optional($this->user->profile)->address ?? '');

        // (your existing stats + recents here, unchanged)
        // ...
    }

    /** Save inline basics (name/phone/address) */
    public function saveBasics(): void
    {
        $this->validateOnly('name');
        $this->validateOnly('phone');
        $this->validateOnly('address');

        $u = $this->user->freshLockForUpdate();
        $u->name = $this->name;
        $u->save();

        $profile = $u->profile()->firstOrNew([]);
        $profile->phone = $this->phone ?: null;
        $profile->address = $this->address ?: null;
        $profile->save();

        $this->dispatchBrowserEvent('alert', ['type'=>'success','message'=>__('Profile updated')]);
        $this->showEditBasics = false;
        $this->user = $u->fresh(['profile']);
    }

    /** Update avatar: crop 1:1 -> S3 */
    public function updateAvatar(): void
    {
        $this->validateOnly('avatarUpload');
        if (!$this->avatarUpload) return;

        $oldKey = optional($this->user->profile)->avatar;

        $key = $this->storeCroppedAvatarToS3($this->avatarUpload, $this->name);

        $profile = $this->user->profile()->firstOrNew([]);
        $profile->avatar = $key;
        $profile->save();

        // best-effort remove old
        if ($oldKey) {
            try {
                if (Storage::disk('s3')->exists($oldKey)) {
                    Storage::disk('s3')->delete($oldKey);
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        $this->avatarUpload = null;
        $this->showAvatarModal = false;

        $this->user = $this->user->fresh(['profile']);

        $this->dispatchBrowserEvent('alert', ['type'=>'success','message'=>__('Photo updated')]);
    }

    /** Remove current avatar */
    public function removeAvatar(): void
    {
        $oldKey = optional($this->user->profile)->avatar;
        if ($oldKey) {
            try {
                if (Storage::disk('s3')->exists($oldKey)) {
                    Storage::disk('s3')->delete($oldKey);
                }
            } catch (\Throwable $e) { /* ignore */ }

            $profile = $this->user->profile()->firstOrNew([]);
            $profile->avatar = null;
            $profile->save();

            $this->user = $this->user->fresh(['profile']);
        }
        $this->dispatchBrowserEvent('alert', ['type'=>'success','message'=>__('Photo removed')]);
    }

    /** Helper: crop & save to S3 with register_first_last_microseconds.jpg */
    protected function storeCroppedAvatarToS3($uploaded, string $fullName): string
    {
        [$first, $last] = $this->splitFirstLast($fullName);
        $first = Str::slug($first ?: 'register');
        $last  = Str::slug($last ?: 'user');
        $usec  = str_replace('.', '', sprintf('%.6f', microtime(true)));
        $key   = "avatar/{$first}_{$last}_{$usec}.jpg";

        $img = Image::read($uploaded->getRealPath())
            ->cover(400, 400)
            ->toJpeg(85)
            ->toString();

        Storage::disk('s3')->put($key, $img, [
            'ACL' => 'public-read',      // or omit if bucket is private
            'ContentType' => 'image/jpeg'
        ]);

        return $key;
    }

    protected function splitFirstLast(string $full): array
    {
        $parts = array_values(array_filter(preg_split('/\s+/u', trim($full) ?: ''), fn($p) => $p !== ''));
        if (count($parts) === 0) return ['', ''];
        if (count($parts) === 1) return [$parts[0], $parts[0]];
        return [$parts[0], $parts[count($parts) - 1]];
    }

    public function render()
    {
        return view('components.general.profile');
    }
}
