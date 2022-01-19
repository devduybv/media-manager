<?php

use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');
$namespace = config('vc-media-manager.namespace');

$api->version('v1', function ($api) use ($namespace) {
    $api->get('test', function (Request $request) {
        return response()->json(['success' => true]);
    });

    $api->group(['prefix' => $namespace], function ($api) {
        //Collection
        $api->get('collections/all', 'VCComponent\Laravel\MediaManager\Http\Controllers\Api\CollectionController@all');
        $api->resource('collections', 'VCComponent\Laravel\MediaManager\Http\Controllers\Api\CollectionController');
        //Media
        $api->get('media/all', 'VCComponent\Laravel\MediaManager\Http\Controllers\Api\MediaController@all');
        $api->post('media/upload', 'VCComponent\Laravel\MediaManager\Http\Controllers\Api\MediaController@upload');
        $api->put('media/{id}/collection/attach', 'VCComponent\Laravel\MediaManager\Http\Controllers\Api\MediaController@attachToCollection');
        $api->put('media/{id}/collection/detach', 'VCComponent\Laravel\MediaManager\Http\Controllers\Api\MediaController@detachFromCollection');
        $api->post('media/bulk', 'VCComponent\Laravel\MediaManager\Http\Controllers\Api\MediaController@createMedias');
        $api->resource('media', 'VCComponent\Laravel\MediaManager\Http\Controllers\Api\MediaController');
    });
});
