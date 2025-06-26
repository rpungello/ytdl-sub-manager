<?php

namespace App\Concerns;

use Illuminate\Support\Arr;
use Symfony\Component\Yaml\Yaml;

trait InteractsWithConfig
{
    protected function loadConfig(): array
    {
        return Yaml::parseFile(config('ytdl-sub.config'));
    }

    protected function loadPresets(): array
    {
        return Arr::get($this->loadConfig(), 'presets', []);
    }

    protected function loadPresetNames(): array
    {
        return array_keys($this->loadPresets());
    }

    protected function loadPresetMenu(): array
    {
        return array_map(
            fn(string $preset) => [
                'id' => $preset,
                'name' => $preset,
            ],
            $this->loadPresetNames()
        );
    }
}
