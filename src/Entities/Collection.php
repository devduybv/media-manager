<?php

namespace VCComponent\Laravel\MediaManager\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use VCComponent\Laravel\MediaManager\Entities\Media;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function setSlugAttribute($value)
    {
        $slug = Str::slug($value);
        $slug = $this->checkUniqueSlug($slug, 0);

        $this->attributes['slug'] = $slug;
    }

    private function checkUniqueSlug($slug, $count)
    {
        $value = $slug;
        $condition = $this->where('slug', $slug)->first();
        $suffix = $count;

        if ($condition) {
            $suffix++;
            if ($count === 0) {
                $value = $value . '-' . $suffix;
            } else {
                $value = Str::replaceLast('-' . $count, '-' . $suffix, $value);
            }
            return $this->checkUniqueSlug($value, $suffix);
        } else {
            return $value;
        }
    }

    public function medias()
    {
        return $this->hasMany(Media::class, 'collection_name', 'slug');
    }
}
