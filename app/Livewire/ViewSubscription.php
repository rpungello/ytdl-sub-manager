<?php

namespace App\Livewire;

use App\Concerns\InteractsWithSubscriptions;
use Carbon\CarbonInterval;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ViewSubscription extends Component
{
    use InteractsWithSubscriptions;

    /**
     * The name of the subscription.
     */
    public string $name;

    /**
     * The path to the archive file for this subscription.
     */
    protected string $path;

    /**
     * The directory containing the subscription files.
     */
    protected string $directory;

    public function mount(string $key): void
    {
        $this->directory = $this->getDirectoryForKey($key);
        $this->name = basename($this->directory);
        if (! $this->archiveFileExists($key, $this->directory)) {
            abort(404);
        }
        $this->path = $this->getArchiveFilePath($key, $this->directory);
    }

    /**
     * Renders the Livewire component view.
     */
    public function render(): View
    {
        return view('livewire.view-subscription');
    }

    /**
     * Computed property for getting a list of videos in the subscription.
     *
     * @return array An array of video information.
     */
    #[Computed]
    public function videos(): array
    {
        $videos = json_decode(
            file_get_contents($this->path),
            true,
        );

        usort($videos, fn (array $a, array $b) => strcmp($b['upload_date'], $a['upload_date']));

        return array_map(fn (array $video) => $this->mapVideo($video), $videos);
    }

    /**
     * Maps raw video data to a standardized format.
     *
     * @param  array  $video  The raw video data.
     * @return array The mapped video information.
     */
    private function mapVideo(array $video): array
    {
        $info = $this->readInfo(
            $this->getInfoPath($video)
        );

        return [
            'title' => $info['title'],
            'resolution' => $this->formatResolution($info['resolution']),
            'upload_date' => $video['upload_date'],
            'size' => array_key_exists('filesize_approx', $info) ? Number::fileSize($info['filesize_approx']) : 'N/A',
            'runtime' => $this->extractFormattedRuntime($info['duration']),
        ];
    }

    /**
     * Reads and returns the info from a given path.
     *
     * @param  string  $path  The file path to read the info from.
     * @return array The decoded JSON content of the file.
     */
    private function readInfo(string $path): array
    {
        return json_decode(
            file_get_contents("$this->directory/$path"),
            true,
        );
    }

    /**
     * Gets the path to the info file within a video.
     *
     * @param  array  $video  The video data containing file names.
     * @return string The path to the info file.
     */
    private function getInfoPath(array $video): string
    {
        return Arr::first($video['file_names'], fn ($file) => str_ends_with($file, '.info.json'));
    }

    /**
     * Extracts and formats the runtime of a video.
     *
     * @param  int  $duration  The duration of the video in seconds.
     * @return string The formatted runtime or 'N/A' if an error occurs.
     */
    private function extractFormattedRuntime($duration): string
    {
        try {
            return CarbonInterval::seconds($duration)->cascade()->forHumans();
        } catch (Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Formats the video resolution to a more readable format.
     *
     * @param  string  $resolution  The raw resolution string.
     * @return string The formatted resolution or the original if not recognized.
     */
    private function formatResolution(string $resolution): string
    {
        return match ($resolution) {
            '640x360' => '360p',
            '854x480' => '480p',
            '1280x720' => '720p',
            '1920x1080' => '1080p',
            '2560x1440' => '1440p',
            '3840x2160' => '4K',
            '7680x4320' => '8K',
            default => $resolution,
        };
    }
}
