<?php

namespace VCComponent\Laravel\MediaManager\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use VCComponent\Laravel\MediaManager\Entities\Media;
use VCComponent\Laravel\MediaManager\Entities\MediaItem;
use VCComponent\Laravel\MediaManager\Repositories\Contracts\MediaRepository;
use VCComponent\Laravel\Vicoders\Core\Exceptions\NotFoundException;

class MediaRepositoryImpl extends BaseRepository implements MediaRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Media::class;
    }

    public function getEntity()
    {
        return $this->model;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function createMedia($url, $collection = null)
    {
        $collection = $collection ? $collection : 'default';
        $media_item = MediaItem::create(['url' => $url]);
        $media = $media_item->addMediaFromUrl($url)->toMediaCollection($collection);
        return $media;
    }
    public function createMedias($urls, $collection = null)
    {
        $collection = $collection ? $collection : 'default';
        $medias = [];

        foreach ($urls as $url) {
            $media_item = MediaItem::create(['url' => $url['url']]);

            $media = $media_item->addMediaFromUrl($url['url'])->toMediaCollection($collection);

            $media->alt_img = $url['alt'];
            $media->save();
            array_push($medias, $media);
        }
        return $medias;
    }

    public function findById($id)
    {
        $item = $this->model->find($id);

        if (!$item) {
            throw new NotFoundException('Media');
        }

        return $item;
    }

    public function deleteById($id)
    {
        $item = $this->model->find($id);

        if (!$item) {
            throw new NotFoundException('Media');
        }

        MediaItem::destroy($item->model_id);
    }

    public function updateCollection($id, $collection)
    {
        $media = $this->findById($id);

        $media->collection_name = $collection;
        $media->save();

        return $media;
    }
}
