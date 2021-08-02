<?php

namespace VCComponent\Laravel\MediaManager\Test;

use Dingo\Api\Provider\LaravelServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
// use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use VCComponent\Laravel\MediaManager\MediaManagerProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelServiceProvider::class,
            MediaManagerProvider::class,
            // MediaLibraryServiceProvider::class,

            // \Spatie\MediaLibrary\MediaLibraryServiceProvider::class,
            // \Spatie\MedialibraryV7UpgradeTool\UpgradeToolServiceProvider::class,
        ];
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withFactories(__DIR__ . '/../tests/Stubs/Factory');
        $this->loadMigrationsFrom(__DIR__ . '/../src/database/migrations');

    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:TEQ1o2POo+3dUuWXamjwGSBx/fsso+viCCg9iFaXNUA=');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'exec' => 'PRAGMA foreign_keys = ON;',
        ]);
        $app['config']->set('vc-media-manager.namespace', 'media-management');
        $app['config']->set('vc-media-manager.models', [
            'vc-media-manager' => \VCComponent\Laravel\MediaManager\Entities\Media::class,

        ]);

        $app['config']->set('vc-media-manager.transformers', [
            'vc-media-manager' => \VCComponent\Laravel\MediaManager\Transformers\MediaTransformer::class,
        ]);
        $app['config']->set('vc-media-manager.thumb_size', [
            'width' => 368,
            'height' => 232,
        ]);

        $app['config']->set('vc-media-manager.auth_middleware', [
            'admin' => [
                'middleware' => '',
            ],
            'frontend' => [
                'middleware' => '',
            ],
        ]);

        $app['config']->set('api', [
            'standardsTree' => 'x',
            'subtype' => '',
            'version' => 'v1',
            'prefix' => 'api',
            'domain' => null,
            'name' => null,
            'conditionalRequest' => true,
            'strict' => false,
            'debug' => true,
            'errorFormat' => [
                'message' => ':message',
                'errors' => ':errors',
                'code' => ':code',
                'status_code' => ':status_code',
                'debug' => ':debug',
            ],
            'middleware' => [
            ],
            'auth' => [
            ],
            'throttling' => [
            ],
            'transformer' => \Dingo\Api\Transformer\Adapter\Fractal::class,
            'defaultFormat' => 'json',
            'formats' => [
                'json' => \Dingo\Api\Http\Response\Format\Json::class,
            ],
            'formatsOptions' => [
                'json' => [
                    'pretty_print' => false,
                    'indent_style' => 'space',
                    'indent_size' => 2,
                ],
            ],
        ]);
        $app['config']->set('filesystems.disks', [
            'media' => [
                'driver' => 'local',
                'url' => env('APP_URL') . '/tests/Stubs',
                'root' => public_path('tests/Stubs'),

            ],
            'local' => [
                'driver' => 'local',
                'root' => storage_path('tests'),
                'url' => env('APP_URL') . '/tests/Stubs',
            ],
            'public' => [
                'driver' => 'local',
                'root' => storage_path('tests'),
                'url' => env('APP_URL') . '/Stubs',
                'visibility' => 'public',
            ],
        ]);
        $app['config']->set('medialibrary', [
            'disk_name' => env('MEDIA_DISK', 'public'),
            'max_file_size' => 1024 * 1024 * 10,
            'media_model' => \VCComponent\Laravel\MediaManager\Entities\Media::class,
        ]);

    }
    public function assertValidation($response, $field, $error_message)
    {
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            "errors" => [
                $field => [
                    $error_message,
                ],
            ],
        ]);
    }
    public function unsetFiled($medias)
    {
        $medias = $medias->map(function ($media) {
            unset($media['updated_at']);
            unset($media['created_at']);
            unset($media['custom_properties']);
            unset($media['manipulations']);
            unset($media['responsive_images']);
            unset($media['order_column']);
            return $media;
        })->toArray();
        return $medias;
    }
}
