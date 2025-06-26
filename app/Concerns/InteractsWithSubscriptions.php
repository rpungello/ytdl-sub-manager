<?php

namespace App\Concerns;

use Symfony\Component\Yaml\Yaml;

trait InteractsWithSubscriptions
{
    protected function loadSubscriptions(): array
    {
        return Yaml::parseFile(config('ytdl-sub.subscriptions'));
    }

    protected function saveSubscriptions(array $subscriptions): void
    {
        $yamlContent = Yaml::dump($subscriptions, 3);
        file_put_contents(config('ytdl-sub.subscriptions'), $yamlContent);
    }
}
