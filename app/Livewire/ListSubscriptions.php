<?php

namespace App\Livewire;

use App\Concerns\InteractsWithConfig;
use App\Concerns\InteractsWithSubscriptions;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
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

    public ?string $filterPreset = null;

    public ?string $removing = null;

    public function mount(): void
    {
        $this->subscriptions = $this->loadSubscriptions();
        $this->presets = $this->loadPresetNames();
    }

    public function render(): View
    {
        return view('livewire.list-subscriptions');
    }

    public function shouldShowSubscription(string $key): bool
    {
        if (empty($this->filterPreset)) {
            return true;
        } else {
            return $this->subscriptions[$key]['preset'] === $this->filterPreset;
        }
    }

    public function save(): void
    {
        $this->saveSubscriptions($this->subscriptions);
        Flux::toast('Subscriptions Saved', variant: 'success');
    }

    public function create(): void
    {
        try {
            $this->subscriptions = $this->appendSubscription($this->newKey, $this->newName, $this->newUrl, $this->newPreset, $this->subscriptions);
            $this->saveSubscriptions($this->subscriptions);

            Flux::toast('Subscription Created', variant: 'success');
            $this->subscriptions = $this->loadSubscriptions();
            $this->newKey = '';
            $this->newName = '';
            $this->newUrl = '';
            $this->newPreset = 'yt_show';
        } catch (ValidationException $e) {
            Flux::toast($e->getMessage(), 'Validation Failed', variant: 'danger');
        }
    }

    public function prepareRemove(string $key): void
    {
        $this->removing = $key;
    }

    public function removeSubscription(): void
    {
        if (empty($this->removing)) {
            return;
        }

        if (array_key_exists($this->removing, $this->subscriptions)) {
            unset($this->subscriptions[$this->removing]);
            $this->saveSubscriptions($this->subscriptions);
        }

        Flux::modal('delete-subscription')->close();
    }
}
