<?php

namespace App\Livewire\Concerns;

trait HasFlashMessage
{
    public ?string $flashMessage = null;

    public string $flashType = 'success';

    protected function flash(string $message, string $type = 'success'): void
    {
        $this->flashMessage = $message;
        $this->flashType = $type;
    }

    public function clearFlash(): void
    {
        $this->flashMessage = null;
    }
}
