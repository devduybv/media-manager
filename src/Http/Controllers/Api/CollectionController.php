<?php

namespace VCComponent\Laravel\MediaManager\Http\Controllers\Api;

use Illuminate\Http\Request;
use VCComponent\Laravel\MediaManager\Repositories\Contracts\CollectionRepository;
use VCComponent\Laravel\MediaManager\Transformers\CollectionTransformer;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class CollectionController extends ApiController
{
    protected $repository;
    protected $entity;
    protected $validator;
    protected $transformer;

    public function __construct(CollectionRepository $repository)
    {
        $this->repository = $repository;
        $this->entity = $repository->getEntity();
        $this->transformer = CollectionTransformer::class;
    }

    public function index(Request $request)
    {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $per_page = (int) $request->has('per_page') ? $request->get('per_page') : 20;

        $collections = $query->paginate($per_page);

        if ($request->has('includes')) {
            $transformer = new $this->transformer($request->get('includes'));
        } else {
            $transformer = new $this->transformer();
        }

        return $this->response->paginator($collections, $transformer);
    }

    public function all(Request $request)
    {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $collections = $query->get();

        if ($request->has('includes')) {
            $transformer = new $this->transformer($request->get('includes'));
        } else {
            $transformer = new $this->transformer();
        }

        return $this->response->collection($collections, $transformer);
    }

    public function show(Request $request, $id)
    {
        $collection = $this->repository->findById($id);

        if ($request->has('includes')) {
            $transformer = new $this->transformer($request->get('includes'));
        } else {
            $transformer = new $this->transformer();
        }

        return $this->response->item($collection, $transformer);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['slug'] = $data['name'];

        $collection = $this->repository->create($data);

        return $this->response->item($collection, new $this->transformer());
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        $collection = $this->repository->updateById($data, $id);

        return $this->response->item($collection, new $this->transformer());
    }

    public function destroy(Request $request, $id)
    {
        $this->repository->deleteById($id);

        return $this->success();
    }
}
