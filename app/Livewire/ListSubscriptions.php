<?php

namespace App\Livewire;

use App\Concerns\InteractsWithConfig;
use App\Concerns\InteractsWithSubscriptions;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListSubscriptions extends Component
{
    use InteractsWithConfig;
    use InteractsWithSubscriptions;

    public array $subscriptions = [];

    public array $presets = [];

    public string $newKey = '';

    public string $newPreset = 'yt_show';

    public string $newName = '';

    public string $newUrl = '';

    public function mount(): void
    {
        $this->subscriptions = $this->loadSubscriptions();
        $this->presets = $this->loadPresetMenu();
    }

    public function render(): View
    {
        return view('livewire.list-subscriptions');
    }

    public function save(): void
    {
        $this->saveSubscriptions($this->subscriptions);
    }

    public function create(): void
    {
        $this->subscriptions = $this->appendSubscription($this->newKey, $this->newName, $this->newUrl, $this->newPreset, $this->subscriptions);
        $this->saveSubscriptions($this->subscriptions);

        $this->redirectRoute('subscriptions.index');
    }
}
