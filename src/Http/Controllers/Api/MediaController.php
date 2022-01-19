<?php

namespace VCComponent\Laravel\MediaManager\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use VCComponent\Laravel\MediaManager\Repositories\Contracts\MediaRepository;
use VCComponent\Laravel\MediaManager\Transformers\MediaTransformer;
use VCComponent\Laravel\Vicoders\Core\Controllers\ApiController;

class MediaController extends ApiController
{
    protected $repository;
    protected $entity;
    protected $transformer;
    const LOCAL_UPLOAD_TYPE = 'local';
    const S3_UPLOAD_TYPE = 's3';

    public function __construct(MediaRepository $repository)
    {
        $this->repository = $repository;
        $this->entity = $repository->getEntity();
        $this->transformer = MediaTransformer::class;
        if (config('media.auth_middleware.admin.middleware') !== '') {
            $this->middleware(
                config('media.auth_middleware.admin.middleware'),
                ['except' => config('media.auth_middleware.admin.except')]
            );
        } else {
            throw new Exception("Admin middleware configuration is required");
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,mp3,mp4,svg'],
        ]);
        $upload_file_type = Config::get('filesystems.default');

        if (!$request->hasFile('file')) {
            throw new NotFoundException("File");
        }
        $file = $request->file('file');
        $origin_name = $file->getClientOriginalName();
        $file_extension = $file->getClientOriginalExtension();
        $file_name = Str::snake(str_replace('.' . $file_extension, '', $origin_name));

        if (!is_null($file_extension)) {

            $path = $this->upload_directory . "/" . time() . "_{$file_name}.{$file_extension}";

        } else {

            $path = $this->upload_directory . "/" . time() . "_{$file_name}";

        }
        $path = strtolower($path);

        $success = Storage::disk($upload_file_type)->put($path, File::get($file));

        if ($success) {

            $url = url($path);

            $collection = "default";
            $media = $this->repository->createMedia($url, $collection);

            return $this->response->item($media, new $this->transformer());

        } else {
            return $this->response->error('can\'t upload file', 1009);
        }

    }

    public function index(Request $request)
    {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $per_page = (int) $request->has('per_page') ? $request->get('per_page') : 20;

        $medias = $query->paginate($per_page);

        if ($request->has('includes')) {
            $transformer = new $this->transformer($request->get('includes'));
        } else {
            $transformer = new $this->transformer();
        }

        return $this->response->paginator($medias, $transformer);
    }

    public function all(Request $request)
    {
        $query = $this->entity;

        $query = $this->applyConstraintsFromRequest($query, $request);
        $query = $this->applySearchFromRequest($query, ['name'], $request);
        $query = $this->applyOrderByFromRequest($query, $request);

        $medias = $query->get();

        if ($request->has('includes')) {
            $transformer = new $this->transformer($request->get('includes'));
        } else {
            $transformer = new $this->transformer();
        }
        return $this->response->collection($medias, $transformer);
    }

    public function show(Request $request, $id)
    {
        $media = $this->repository->findById($id);

        if ($request->has('includes')) {
            $transformer = new $this->transformer($request->get('includes'));
        } else {
            $transformer = new $this->transformer();
        }

        return $this->response->item($media, $transformer);
    }
    public function createMedias(Request $request)
    {

        $urls = $request->input('urls');
        $collection = $request->has('collection') ? $request->input('collection') : null;
        $media = $this->repository->createMedias($urls, $collection);

        return $this->response->collection(collect($media), new $this->transformer());
    }

    public function store(Request $request)
    {
        $url = $request->input('url');
        $collection = $request->has('collection') ? $request->input('collection') : null;
        $media = $this->repository->createMedia($url, $collection);

        return $this->response->item($media, new $this->transformer());
    }

    public function destroy(Request $request, $id)
    {
        $this->repository->deleteById($id);

        return $this->success();
    }

    public function attachToCollection(Request $request, $id)
    {
        $media = $this->repository->updateCollection($id, $request->input('collection'));
        return $this->response->item($media, new $this->transformer());
    }

    public function detachFromCollection(Request $request, $id)
    {
        $media = $this->repository->updateCollection($id, 'default');
        return $this->response->item($media, new $this->transformer());
    }
}
