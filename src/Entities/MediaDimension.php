<?php

namespace VCComponent\Laravel\MediaManager\Entities;

use Illuminate\Database\Eloquent\Model;

class MediaDimension extends Model
{
    protected $fillable = [
        'id',
        'model',
        'type',
        'name',
        'width',
        'height'
    ];
}
