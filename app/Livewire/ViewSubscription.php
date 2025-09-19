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

    public string $name;

    protected string $path;

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

    public function render(): View
    {
        return view('livewire.view-subscription');
    }

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

    private function readInfo(string $path): array
    {
        return json_decode(
            file_get_contents("$this->directory/$path"),
            true,
        );
    }

    private function getInfoPath(array $video): string
    {
        return Arr::first($video['file_names'], fn ($file) => str_ends_with($file, '.info.json'));
    }

    private function extractFormattedRuntime($duration): string
    {
        try {
            return CarbonInterval::seconds($duration)->cascade()->forHumans();
        } catch (Exception $e) {
            return 'N/A';
        }
    }

    private function formatResolution(string $resolution): string
    {
        return match ($resolution) {
            '1920x1080' => '1080p',
            '1280x720' => '720p',
            '3840x2160' => '4K',
            '7680x4320' => '8K',
            default => $resolution,
        };
    }
}
