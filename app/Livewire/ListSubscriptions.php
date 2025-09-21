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

    /**
     * Initialize the component.
     * Load subscriptions and presets from data sources.
     */
    public function mount(): void
    {
        $this->subscriptions = $this->loadSubscriptions();
        $this->presets = $this->loadPresetNames();
    }

    /**
     * Render the Livewire component view.
     */
    public function render(): View
    {
        return view('livewire.list-subscriptions');
    }

    /**
     * Determine if a subscription should be shown based on the filter preset.
     *
     * @param  string  $key  The key of the subscription to check.
     * @return bool True if the subscription should be shown, false otherwise.
     */
    public function shouldShowSubscription(string $key): bool
    {
        if (empty($this->filterPreset)) {
            return true;
        } else {
            return $this->subscriptions[$key]['preset'] === $this->filterPreset;
        }
    }

    /**
     * Save the current list of subscriptions.
     */
    public function save(): void
    {
        $this->saveSubscriptions($this->subscriptions);
        Flux::toast('Subscriptions Saved', variant: 'success');
    }

    /**
     * Create a new subscription with provided details.
     */
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

    /**
     * Prepare to remove a subscription by setting its key.
     *
     * @param  string  $key  The key of the subscription to be removed.
     */
    public function prepareRemove(string $key): void
    {
        $this->removing = $key;
    }

    /**
     * Remove the specified subscription from the list.
     */
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

    /**
     * Get the number of episodes for a given subscription key.
     *
     * @param  string  $key  The key associated with the subscription to check.
     * @return int The count of episodes.
     */
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

    /**
     * Count the number of episodes in a given directory recursively.
     *
     * @param  string  $directory  The path to the directory containing episodes.
     * @return int The count of episodes found.
     */
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
     * Get the list of video file extensions supported by the system.
     *
     * @return string[] An array of video file extensions.
     */
    private function getVideoExtensions(): array
    {
        return ['mp4', 'webm'];
    }
}
