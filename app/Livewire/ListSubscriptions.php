<?php

namespace App\Livewire;

use App\Concerns\InteractsWithConfig;
use App\Concerns\InteractsWithSubscriptions;
use Carbon\CarbonInterval;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use UnexpectedValueException;

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

    public function getNumberOfEpisodes(string $key): int
    {
        $directory = $this->getDirectoryForKey($key, $this->subscriptions);

        if ($this->archiveFileExists($key, $directory)) {
            $json = json_decode(
                file_get_contents($this->getArchiveFilePath($key, $directory)),
                true
            );

            if (json_last_error() === JSON_ERROR_NONE) {
                return count($json);
            }
        }

        return Cache::remember(
            "shows.$key.episodes",
            CarbonInterval::createFromDateString('1 hour'),
            fn () => $this->countEpisodesInDirectory($directory)
        );
    }

    private function countEpisodesInDirectory(string $directory): int
    {
        $count = 0;
        try {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
            foreach ($iterator as $file) {
                if ($file->isFile() && in_array($file->getExtension(), $this->getVideoExtensions())) {
                    $count++;
                }
            }

            return $count;
        } catch (UnexpectedValueException) {
            return 0;
        }
    }

    /**
     * @return string[]
     */
    private function getVideoExtensions(): array
    {
        return ['mp4', 'webm'];
    }
}
