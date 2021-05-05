<?php

namespace VCComponent\Laravel\MediaManager\Services;

trait MediaQueryTrait
{
    public function getMediaQuery($model_id)
    {
        $query = $this->query->select()->where('model_id', $model_id)->get();

        return $query;
    }
}
