<?php

namespace App\Livewire;

use App\Concerns\InteractsWithConfig;
use App\Concerns\InteractsWithSubscriptions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
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

    public function mount(): void
    {
        $this->subscriptions = $this->loadSubscriptions();
        $this->presets = $this->loadPresetMenu();
    }

    public function render(): View
    {
        return view('livewire.list-subscriptions');
    }

    public function filter(): void
    {
        $this->subscriptions = $this->loadSubscriptions($this->filterPreset);
    }

    public function save(): void
    {
        $this->saveSubscriptions($this->subscriptions);
        LivewireAlert::success()->title('Subscriptions Saved')->toast()->show();
    }

    public function create(): void
    {
        try {
            $this->subscriptions = $this->appendSubscription($this->newKey, $this->newName, $this->newUrl, $this->newPreset, $this->subscriptions);
            $this->saveSubscriptions($this->subscriptions);

            LivewireAlert::success()->title('Subscription Created')->toast()->show();
            $this->subscriptions = $this->loadSubscriptions();
            $this->newKey = '';
            $this->newName = '';
            $this->newUrl = '';
            $this->newPreset = 'yt_show';
        } catch (ValidationException $e) {
            LivewireAlert::title('Validation Failed')
                ->text($e->getMessage())
                ->error()
                ->toast()
                ->show();
        }
    }

    public function remove(string $key): void
    {
        LivewireAlert::title('Remove Subscription?')
            ->text(Arr::get($this->subscriptions, "$key.overrides.tv_show_name", $key))
            ->onConfirm('removeSubscription', ['key' => $key])
            ->asConfirm()
            ->show();
    }

    public function removeSubscription(array $data): void
    {
        $key = Arr::get($data, 'key');

        if (array_key_exists($key, $this->subscriptions)) {
            unset($this->subscriptions[$key]);
            $this->saveSubscriptions($this->subscriptions);
        }
    }
}
