<?php

namespace App\Http\Livewire\General;

use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class AnnouncementLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters / sorting
    public string $q = '';
    public ?string $visibleFilter = null; // '1','0',null
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    // Modal state
    public bool $showModal = false;
    public ?int $editId = null;

    // Form fields
    public string $body = '';
    public bool $is_visible = false;
    public ?string $show_from = null;   // 'Y-m-d H:i'
    public ?string $show_until = null;  // 'Y-m-d H:i'

    protected function rules(): array
    {
        return [
            'body'        => ['required', 'string', 'min:3'],
            'is_visible'  => ['boolean'],
            'show_from'   => ['nullable', 'date'],
            'show_until'  => ['nullable', 'date', 'after_or_equal:show_from'],
        ];
    }

    public function mount(): void
    {
        // you can gate here if needed, e.g. only Admins
        // abort_unless(Auth::user()?->role === 'Admin', 403);
    }

    public function updatingQ(): void { $this->resetPage(); }
    public function updatedVisibleFilter(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }

    public function sort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function rows()
    {
        return Announcement::query()
            ->with('creator')
            ->when($this->q !== '', function ($q) {
                $q->where('body', 'like', '%'.$this->q.'%');
            })
            ->when($this->visibleFilter !== null && $this->visibleFilter !== '', function ($q) {
                $q->where('is_visible', (bool) ((int) $this->visibleFilter));
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $row = Announcement::findOrFail($id);
        $this->editId     = $row->id;
        $this->body       = (string) $row->body;
        $this->is_visible = (bool) $row->is_visible;
        $this->show_from  = optional($row->show_from)->format('Y-m-d H:i');
        $this->show_until = optional($row->show_until)->format('Y-m-d H:i');
        $this->showModal  = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        // Normalize empty -> null
        $data['show_from']  = $data['show_from'] ?: null;
        $data['show_until'] = $data['show_until'] ?: null;

        if ($this->editId) {
            $row = Announcement::findOrFail($this->editId);
            $row->update($data);
            $this->dispatchBrowserEvent('toast', ['message' => __('Announcement updated')]);
        } else {
            Announcement::create([
                'created_by'  => Auth::id(),
                'body'        => $data['body'],
                'is_visible'  => $data['is_visible'] ?? false,
                'show_from'   => $data['show_from'] ?? null,
                'show_until'  => $data['show_until'] ?? null,
                'audience_roles' => null, // future use
            ]);
            $this->dispatchBrowserEvent('toast', ['message' => __('Announcement created')]);
        }

        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();
    }

    public function toggleVisible(int $id): void
    {
        $row = Announcement::findOrFail($id);
        $row->update(['is_visible' => !$row->is_visible]);
        $this->dispatchBrowserEvent('toast', ['message' => $row->is_visible ? __('Shown') : __('Hidden')]);
    }

    public function delete(int $id): void
    {
        $row = Announcement::findOrFail($id);
        $row->delete();
        $this->dispatchBrowserEvent('toast', ['message' => __('Announcement deleted')]);
        if ($this->rows()->isEmpty() && $this->page > 1) $this->previousPage();
    }

    protected function resetForm(): void
    {
        $this->resetValidation();
        $this->editId = null;
        $this->body = '';
        $this->is_visible = false;
        $this->show_from = null;
        $this->show_until = null;
    }

    public function render()
    {
        return view('components.tables.announcement', [
            'rows' => $this->rows(),
        ]);
    }
}
