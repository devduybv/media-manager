<?php

namespace VCComponent\Laravel\MediaManager\Transformers;

use League\Fractal\TransformerAbstract;

class MediaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [];

    public function __construct($includes = [])
    {
        $this->setDefaultIncludes($includes);
    }

    public function transform($model)
    {
        return [
            'id'              => $model->id,
            'model_id'        => $model->model_id,
            'model_type'      => $model->model_type,
            'collection_name' => $model->collection_name,
            'name'            => $model->name,
            'file_name'       => $model->file_name,
            'mime_type'       => $model->mime_type,
            'disk'            => $model->disk,
            'size'            => $model->size,
            'alt_img'         => $model->alt_img,
            'order'           => $model->order_column,
            'original_url'    => $model->getFullUrl(),
            'thumb_url'       => $model->getFullUrl(''),
            'timestamps'      => [
                'created_at' => $model->created_at,
                'updated_at' => $model->updated_at,
            ],
        ];
    }
}
