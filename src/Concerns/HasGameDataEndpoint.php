<?php

namespace Habbo\Gamedata\Concerns;

use Illuminate\Support\Facades\Cache;

trait HasGameDataEndpoint
{
    protected string $cacheKey;

    protected string $path;

    abstract public function content(): string;

    public function hash(): string
    {
        return sha1($this->content());
    }

    public function url(): string
    {
        return url($this->path);
    }

    public function hashedUrl(): string
    {
        return url($this->path . '/' . $this->hash());
    }

    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
        $this->clearDependentCaches();
    }

    protected function clearDependentCaches(): void
    {
        // Override in services where other endpoints depend on this cache.
    }
}
