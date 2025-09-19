<?php

namespace App\Livewire;

use App\Concerns\InteractsWithSubscriptions;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ViewSubscription extends Component
{
    use InteractsWithSubscriptions;

    public string $name;

    protected string $path;

    public function mount(string $key): void
    {
        $directory = $this->getDirectoryForKey($key);
        $this->name = basename($directory);
        if (! $this->archiveFileExists($key, $directory)) {
            abort(404);
        }
        $this->path = $this->getArchiveFilePath($key, $directory);
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

        return $videos;
    }
}
