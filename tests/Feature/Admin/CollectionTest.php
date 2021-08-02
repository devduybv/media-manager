<?php

namespace VCComponent\Laravel\MediaManager\Test\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\MediaManager\Entities\Collection;
use VCComponent\Laravel\MediaManager\Test\TestCase;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_get_collection_list_admin()
    {
        $collections = factory(Collection::class, 5)->create()->toArray();
        $response = $this->json('GET', 'api/media-management/collections/all');
        $response->assertStatus(200);
        foreach ($collections as $item) {
            $response->assertJsonFragment([
                'name' => $item['name'],
                'description' => $item['description'],
            ]);
        }

    }

    /**
     * @test
     */
    public function should_get_collection_list_with_constraints_admin()
    {
        $collections = factory(Collection::class, 5)->create();
        $name_constraints = $collections[0]->name;
        $collections = $collections->map(function ($collection) {
            unset($collection['created_at']);
            unset($collection['updated_at']);
            unset($collection['order']);
            return $collection;
        })->toArray();

        $constraints = '{"name":"' . $name_constraints . '"}';

        $response = $this->json('GET', 'api/media-management/collections/all?constraints=' . $constraints);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$collections[0]],
        ]);
    }
    /**
     * @test
     */

    public function should_get_collection_list_with_search_admin()
    {
        factory(Collection::class, 5)->create();
        $collection = factory(Collection::class)->create(['name' => 'avata'])->toArray();
        unset($collection['created_at']);
        unset($collection['updated_at']);
        unset($collection['order']);

        $response = $this->json('GET', 'api/media-management/collections/all?search=avata');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$collection],
        ]);
    }

    /**
     * @test
     */

    public function should_get_collection_list_with_order_admin()
    {
        $collections = factory(Collection::class, 5)->create();
        $collections = $collections->map(function ($collection) {
            unset($collection['created_at']);
            unset($collection['updated_at']);
            unset($collection['order']);
            return $collection;
        })->toArray();
        $order_by = '{"id":"desc"}';
        $listId = array_column($collections, 'id');
        array_multisort($listId, SORT_DESC, $collections);

        $response = $this->json('GET', 'api/media-management/collections/all?order_by=' . $order_by);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => $collections,
        ]);
    }

    /**
     * @test
     */
    public function should_get_collection_list_paginate_admin()
    {
        $collections = factory(Collection::class, 5)->create()->toArray();
        $response = $this->json('GET', 'api/media-management/collections');
        $response->assertStatus(200);
        foreach ($collections as $item) {
            $response->assertJsonFragment([
                'name' => $item['name'],
                'description' => $item['description'],
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
    public function should_get_collection_list_with_constraints_paginate_admin()
    {
        $collections = factory(Collection::class, 5)->create();
        $name_constraints = $collections[0]->name;
        $collections = $collections->map(function ($collection) {
            unset($collection['created_at']);
            unset($collection['updated_at']);
            unset($collection['order']);
            return $collection;
        })->toArray();

        $constraints = '{"name":"' . $name_constraints . '"}';

        $response = $this->json('GET', 'api/media-management/collections?constraints=' . $constraints);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$collections[0]],
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
    public function should_get_collection_list_with_search_paginate_admin()
    {
        factory(Collection::class, 5)->create();
        $collection = factory(Collection::class)->create(['name' => 'avata'])->toArray();
        unset($collection['created_at']);
        unset($collection['updated_at']);
        unset($collection['order']);

        $response = $this->json('GET', 'api/media-management/collections?search=avata');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [$collection],
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
    public function should_get_collection_list_with_order_paginate_admin()
    {
        $collections = factory(Collection::class, 5)->create();
        $collections = $collections->map(function ($collection) {
            unset($collection['created_at']);
            unset($collection['updated_at']);
            unset($collection['order']);
            return $collection;
        })->toArray();
        $order_by = '{"id":"desc"}';
        $listId = array_column($collections, 'id');
        array_multisort($listId, SORT_DESC, $collections);

        $response = $this->json('GET', 'api/media-management/collections?order_by=' . $order_by);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => $collections,
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
    public function should_not_get_collection_not_exists_item_admin()
    {
        factory(Collection::class)->create();
        $response = $this->json('GET', 'api/media-management/collections/2');
        $response->assertStatus(400);
        $response->assertJson(["message" => "Collection not found"]);

    }

    /**
     * @test
     */
    public function should_get_collection_item_admin()
    {
        $collection = factory(Collection::class)->create()->toArray();
        unset($collection['created_at']);
        unset($collection['updated_at']);
        unset($collection['order']);
        $response = $this->json('GET', 'api/media-management/collections/' . $collection['id']);
        $response->assertStatus(200);
        $response->assertJson(['data' => $collection]);
    }

    /**
     * @test
     */
    public function should_create_collection_exists_admin()
    {
        factory(Collection::class)->create(['name' => 'avatar', 'slug' => 'avatar']);
        $data = factory(Collection::class)->make(['name' => 'avatar'])->toArray();
        unset($data['created_at']);
        unset($data['updated_at']);
        unset($data['order']);
        unset($data['slug']);

        $response = $this->json('POST', 'api/media-management/collections', $data);
        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $response->assertJsonMissing(['slug' => 'avatar']);
        $this->assertDatabaseHas('collections', $data);

    }
    /**
     * @test
     */

    public function should_not_create_collection_required_admin()
    {
        $data = factory(Collection::class)->make(['name' => ''])->toArray();
        $response = $this->json('POST', 'api/media-management/collections', $data);
        $response->assertStatus(500);

    }
    /**
     * @test
     */

    public function should_create_collection_admin()
    {
        $data = factory(Collection::class)->make()->toArray();
        unset($data['created_at']);
        unset($data['updated_at']);
        unset($data['order']);

        $response = $this->json('POST', 'api/media-management/collections', $data);
        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);
        $this->assertDatabaseHas('collections', $data);
    }

    /**
     * @test
     */

    public function should_not_update_collection_not_exists_admin()
    {
        $collection = factory(Collection::class)->create();
        $id = $collection->id;
        $collection->name = 'update name';
        $data = $collection->toArray();
        $response = $this->json('PUT', 'api/media-management/collections/2', $data);
        $response->assertStatus(400);
        $response->assertJson(["message" => "Collection not found"]);

    }

    /**
     * @test
     */
    public function should_not_update_collection_required_admin()
    {
        $collection = factory(Collection::class)->create();
        $id = $collection->id;
        $collection->name = '';
        $data = $collection->toArray();
        $response = $this->json('PUT', 'api/media-management/collections/' . $id, $data);
        $response->assertStatus(500);
    }

    /**
     * @test
     */
    public function should_update_collection_admin()
    {
        $collection = factory(Collection::class)->create();
        $id = $collection->id;
        $collection->name = 'update name';
        $collection->description = 'update description';
        $data = $collection->toArray();
        unset($data['updated_at']);
        unset($data['created_at']);
        unset($data['slug']);

        $response = $this->json('PUT', 'api/media-management/collections/' . $id, $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('collections', $data);
    }

    /**
     * @test
     */
    public function should_soft_delete_collection_admin()
    {
        $collection = factory(Collection::class)->create()->toArray();
        unset($collection['updated_at']);
        unset($collection['created_at']);
        $this->assertDatabaseHas('collections', $collection);
        $response = $this->json('DELETE', 'api/media-management/collections/' . $collection['id']);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDeleted('collections', $collection);
    }
    /**
     * @test
     */
    public function should_not_delete_collection_not_exists_item_admin()
    {
        factory(Collection::class)->create();
        $response = $this->json('DELETE', 'api/media-management/collections/2');
        $response->assertStatus(400);
        $response->assertJson(["message" => "Collection not found"]);
    }

}
