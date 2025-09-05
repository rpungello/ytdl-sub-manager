<?php

namespace App\Concerns;

use Illuminate\Support\Arr;
use Symfony\Component\Yaml\Yaml;

trait InteractsWithConfig
{
    protected function loadConfig(): array
    {
        $path = config('ytdl-sub.config');
        if (! file_exists($path)) {
            file_put_contents($path, Yaml::dump([]));
        }

        return Yaml::parseFile($path);
    }

    protected function loadPresets(): array
    {
        return Arr::get($this->loadConfig(), 'presets', []);
    }

    protected function loadPresetNames(): array
    {
        return array_keys($this->loadPresets());
    }
}
