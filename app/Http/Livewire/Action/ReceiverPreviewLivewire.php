<?php

namespace App\Http\Livewire\Action;

use Livewire\Component;
use App\Models\Receiver;
use Illuminate\Support\Facades\Storage;

class ReceiverPreviewLivewire extends Component
{
    public int $receiverId;
    public ?Receiver $receiver = null;

    public bool $showModal = false;
    /** normalized identification files for the view */
    public array $identFiles = [];

    public function mount(int $receiverId): void
    {
        $this->receiverId = $receiverId;
        $this->loadReceiver();
    }

    public function open(): void
    {
        $this->loadReceiver();
        $this->dispatchBrowserEvent('receiver-preview:open', ['id' => $this->receiverId]);
    }

    public function close(): void
    {
        $this->dispatchBrowserEvent('receiver-preview:close', ['id' => $this->receiverId]);
    }

    protected function loadReceiver(): void
    {
        $this->receiver = Receiver::find($this->receiverId);
        $this->identFiles = $this->normalizeIdentification($this->receiver?->identification);
    }

    protected function normalizeIdentification($ident): array
    {
        $keys = [];
        if (is_string($ident) && $ident !== '') $keys = [$ident];
        elseif (is_array($ident)) {
            foreach ($ident as $item) {
                $keys[] = is_string($item) ? $item : ($item['key'] ?? $item['path'] ?? '');
            }
            $keys = array_values(array_filter($keys));
        }

        return array_map(function ($key) {
            $isPdf = str_ends_with(strtolower($key), '.pdf');
            // use temporaryUrl(...) if your bucket is private
            $url = Storage::disk('s3')->url($key);
            return [
                'key' => $key,
                'url' => $url,
                'is_image' => !$isPdf,
                'is_pdf' => $isPdf,
                'name' => basename($key),
            ];
        }, $keys);
    }

    public function render()
    {
        // IMPORTANT: make sure this matches your blade path
        return view('components.reuse.receiver-preview', [
            'receiver'   => $this->receiver,
            'identFiles' => $this->identFiles,
            'showModal'  => $this->showModal,
        ]);
    }
}
