<?php

namespace VCComponent\Laravel\MediaManager;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use VCComponent\Laravel\MediaManager\Entities\MediaItem;
use VCComponent\Laravel\MediaManager\Services\Media;
use VCComponent\Laravel\MediaManager\Repositories\CollectionRepositoryImpl;
use VCComponent\Laravel\MediaManager\Repositories\Contracts\CollectionRepository;
use VCComponent\Laravel\MediaManager\Repositories\Contracts\MediaRepository;
use VCComponent\Laravel\MediaManager\Repositories\MediaRepositoryImpl;

class MediaManagerProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind("media", Media::class);
        $this->app->bind(CollectionMediaRepository::class, CollectionRepositoryImpl::class);
        $this->app->bind(MediaRepository::class, MediaRepositoryImpl::class);
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->publishes([
            __DIR__ . '/../config/vc-media-manager.php' => config_path('vc-media-manager.php'),
        ], 'config');

        Relation::morphMap([
            'media' => MediaItem::class,
        ]);

    }
}
