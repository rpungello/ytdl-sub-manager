<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Yaml\Yaml;

trait InteractsWithSubscriptions
{
    protected function loadSubscriptions(?string $preset = null): array
    {
        $path = config('ytdl-sub.subscriptions');
        if (! file_exists($path)) {
            file_put_contents($path, Yaml::dump([]));
        }

        $subscriptions = Yaml::parseFile($path);
        if (! empty($preset)) {
            $subscriptions = array_filter($subscriptions, fn (array $subscription) => $subscription['preset'] === $preset);
        }
        ksort($subscriptions);

        return $subscriptions;
    }

    protected function saveSubscriptions(array $subscriptions): void
    {
        $yamlContent = Yaml::dump($subscriptions, 3);
        file_put_contents(config('ytdl-sub.subscriptions'), $yamlContent);
    }

    /**
     * @throws ValidationException
     */
    protected function appendSubscription(string $key, string $name, string $url, string $preset, array $existing = []): array
    {
        Validator::validate(compact('key', 'name', 'url', 'preset', 'existing'), [
            'key' => ['required', 'regex:/[a-z]+/'],
            'name' => ['required'],
            'url' => ['required'],
            'preset' => ['required', 'in:yt_show,yt_show_all'],
        ]);

        $existing[$key] = [
            'preset' => $preset,
            'overrides' => [
                'tv_show_name' => $name,
                'url' => $url,
            ],
        ];

        return $existing;
    }
}
