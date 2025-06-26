<?php

namespace App\Livewire;

use App\Concerns\InteractsWithSubscriptions;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListSubscriptions extends Component
{
    use InteractsWithSubscriptions;

    public array $subscriptions = [];

    public function mount(): void
    {
        $this->subscriptions = $this->loadSubscriptions();
    }

    public function render(): View
    {
        return view('livewire.list-subscriptions');
    }

    public function save(string $key): void
    {
        $this->saveSubscriptions($this->subscriptions);
    }
}
