<?php

namespace VCComponent\Laravel\MediaManager\Services;

use VCComponent\Laravel\MediaManager\Entities\Media as BaseModel;
use VCComponent\Laravel\MediaManager\Services\MediaQueryTrait;
use Illuminate\Support\Facades\Cache;

class Media
{
    use MediaQueryTrait;

    public $query;
    protected $cache        = false;
    protected $cacheMinutes = 60;

    public function __construct()
    {
        $this->query = new BaseModel;
    }

    public function get_media($model_id)
    {
        if ($this->cache === true) {
            if (Cache::has('get_media') && Cache::get('get_media')->count() !== 0) {
                return Cache::get('get_media');
            }
            return Cache::remember('get_media', $this->timeCache, function () use ($model_id) {
                return $this->getMediaQuery($model_id);
            });
        }
        return $this->getMediaQuery($model_id);

    }
}
