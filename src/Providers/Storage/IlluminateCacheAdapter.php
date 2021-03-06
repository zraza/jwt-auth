<?php

namespace Tymon\JWTAuth\Providers\Storage;

use Illuminate\Cache\CacheManager;
use Tymon\JWTAuth\Providers\Storage\StorageInterface;

class IlluminateCacheAdapter implements StorageInterface
{
    /**
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * @var string
     */
    protected $tag = 'tymon.jwt';

    /**
     * @param \Illuminate\Cache\CacheManager  $cache
     */
    public function __construct(CacheManager  $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Add a new item into storage
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $minutes
     * @return void
     */
    public function add($key, $value, $minutes)
    {
        return $this->cache->tags($this->tag)->put($key, $value, $minutes);
    }

    /**
     * Check whether a key exists in storage
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return $this->cache->tags($this->tag)->has($key);
    }

    /**
     * Remove an item from storage
     *
     * @param  string  $key
     * @return bool
     */
    public function destroy($key)
    {
        return $this->cache->tags($this->tag)->forget($key);
    }

    /**
     * Remove all items associated with the tag
     *
     * @return void
     */
    public function flush()
    {
        return $this->cache->tags($this->tag)->flush();
    }
}
