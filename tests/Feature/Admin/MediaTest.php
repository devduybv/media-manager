<?php

namespace VCComponent\Laravel\Language\Test\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\MediaManager\Entities\Media;
use VCComponent\Laravel\MediaManager\Test\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_get_media_list_admin()
    {
        $medias = factory(Media::class, 5)->create()->toArray();
        $response = $this->json('GET', 'api/media-management/media/all');
        $response->assertStatus(200);
        foreach ($medias as $item) {
            $response->assertJsonFragment([
                'model_type' => $item['model_type'],
                'name' => $item['name'],
                'file_name' => $item['file_name'],
            ]);
        }
    }

    /**
     * @test
     */
    public function should_get_media_list_with_constraints_admin()
    {
        $medias = factory(Media::class, 5)->create();
        $name_constraints = $medias[0]->name;
        $constraints = '{"name":"' . $name_constraints . '"}';
        $response = $this->json('GET', 'api/media-management/media/all?constraints=' . $constraints);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            "name" => $medias[0]['name'],
            "file_name" => $medias[0]['file_name'],
        ]);
    }

    /**
     * @test
     */

    public function should_get_media_list_with_search_admin()
    {
        factory(Media::class, 5)->create();
        $media = factory(Media::class)->create(['name' => 'avata'])->toArray();
        $response = $this->json('GET', 'api/media-management/media/all?search=avata');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'avata',
            'id' => $media['id'],
        ]);
    }

    /**
     * @test
     */

    public function should_get_media_list_with_order_admin()
    {
        $medias = factory(Media::class, 5)->create();
        $medias = $this->unsetFiled($medias);
        $order_by = '{"id":"desc"}';
        $listId = array_column($medias, 'id');
        array_multisort($listId, SORT_DESC, $medias);
        $response = $this->json('GET', 'api/media-management/media/all?order_by=' . $order_by);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => $medias,
        ]);
    }

    /**
     * @test
     */
    public function should_get_media_list_paginate_admin()
    {
        $medias = factory(Media::class, 5)->create();

        $response = $this->json('GET', 'api/media-management/media');
        $response->assertStatus(200);
        foreach ($medias as $item) {
            $response->assertJsonFragment([
                'name' => $item['name'],
                'file_name' => $item['file_name'],
            ]);
        }
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function should_get_media_list_with_constraints_paginate_admin()
    {
        $medias = factory(Media::class, 5)->create();
        $name_constraints = $medias[0]->name;
        $collection_constraints = $medias[0]->collection_name;
        $constraints = '{"name":"' . $name_constraints . '", "collection_name":"' . $collection_constraints . '"}';
        $response = $this->json('GET', 'api/media-management/media?constraints=' . $constraints);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $medias[0]['name'],
            'file_name' => $medias[0]['file_name'],
        ]);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function should_get_media_list_with_search_paginate_admin()
    {
        $medias = factory(Media::class, 5)->create();
        $media = factory(Media::class)->create(['name' => 'avata']);
        $response = $this->json('GET', 'api/media-management/media?search=avata');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $media->name,
        ]);
        foreach ($medias as $item) {
            $response->assertJsonMissing([
                'name' => $item->name,
            ]);
        }
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
    }

    /**
     * @test
     */

    public function should_get_media_list_with_order_paginate_admin()
    {
        $medias = factory(Media::class, 5)->create();
        $medias = $this->unsetFiled($medias);
        $order_by = '{"id":"desc"}';
        $listId = array_column($medias, 'id');
        array_multisort($listId, SORT_DESC, $medias);

        $response = $this->json('GET', 'api/media-management/media?order_by=' . $order_by);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => $medias,
        ]);
    }

    /**
     * @test
     */
    public function should_attach_collection_media_admin()
    {

        $media = factory(Media::class)->create(['collection_name' => 'avata']);
        $data['collection'] = "library";
        $response = $this->json('PUT', 'api/media-management/media/' . $media->id . '/collection/attach', $data);
        $response->assertStatus(200);

        $response->assertJsonFragment(["collection_name" => "library"]);
        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'collection_name' => "library",
        ]);

    }

    /**
     * @test
     */

    public function should_detach_collection_media_admin()
    {
        $media = factory(Media::class)->create(['collection_name' => 'avata']);
        $response = $this->json('PUT', 'api/media-management/media/' . $media->id . '/collection/detach');
        $response->assertStatus(200);
        $response->assertJsonFragment(["collection_name" => "default"]);
        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'collection_name' => "default",
        ]);

    }

    /**
     * @test
     */
    public function should_create_media_admin()
    {
        $url = dirname(__DIR__, 2) . '\Stubs\test.png';
        $response = $this->json('POST', 'api/media-management/media', ['url' => $url]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('media', [
            'name' => 'test',
            'file_name' => 'test.png',
        ]);
        $this->assertDatabaseHas('media_items', [
            'url' => $url,
        ]);
    }

    /**
     * @test
     */
    public function should_create_bulk_media_admin()
    {
        $urls = [
            ['url' => dirname(__DIR__, 2) . '\Stubs\test.png',
                'alt' => 'test',
            ],
            ['url' => dirname(__DIR__, 2) . '\Stubs\avata.jpg',
                'alt' => 'avata',
            ],

        ];
        $response = $this->json('POST', 'api/media-management/media/bulk', ['urls' => $urls]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('media', [
            'name' => 'test',
        ]);
        $this->assertDatabaseHas('media', [
            'name' => 'avata',
        ]);
        foreach ($urls as $url) {
            $this->assertDatabaseHas('media_items', [
                'url' => $url['url'],
            ]);

        }
    }

    /**
     * @test
     */
    public function should_not_get_media_item_not_exists_admin()
    {
        $media = factory(Media::class)->create();
        unset($media['updated_at']);
        unset($media['created_at']);
        $response = $this->json('GET', 'api/media-management/media/2');
        $response->assertStatus(400);
        $response->assertJson(['message' => "Media not found"]);

    }
    /**
     * @test
     */
    public function should_get_media_item_admin()
    {
        $media = factory(Media::class)->create()->toArray();
        $response = $this->json('GET', 'api/media-management/media/' . $media['id']);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            "name" => $media['name'],
            "file_name" => $media['file_name'],
        ]);
    }

    /**
     * @test
     */
    public function should_not_soft_delete_media_not_exists_admin()
    {
        factory(Media::class)->create()->toArray();
        $response = $this->json('DELETE', 'api/media-management/media/2');
        $response->assertStatus(400);
        $response->assertJson(['message' => 'Media not found']);

    }
    /**
     * @test
     */
    public function should_soft_delete_media_admin()
    {
        $media = factory(Media::class)->create()->toArray();
        $this->assertDatabaseHas('media', [
            'id' => $media['id'],
        ]);
        $response = $this->json('DELETE', 'api/media-management/media/' . $media['id']);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('media_items', ['id' => $media['model_id']]);

    }

}
