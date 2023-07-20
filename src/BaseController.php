<?php

namespace GustavoSantarosa\LaravelToolPack;

use GustavoSantarosa\LaravelToolPack\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use ApiResponse;

    protected $service;
    protected $resource;
    protected $model;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new DefaultCollection($this->resource, $this->service->index());
    }

    /**
     * Store the specified resource from storage.
     *
     * @param Request $request
     */
    public function store()
    {
        return $this->okResponse(
            new $this->resource(
                $this->service->store()
            )
        );
    }

    /**
     * Atualizar.
     *
     * @param mixed $request
     * @param mixed $id
     */
    public function update($id)
    {
        return $this->okResponse(
            new $this->resource(
                $this->service->update($id)
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     */
    public function show($id): JsonResponse
    {
        return $this->okResponse(
            new $this->resource(
                $this->service->show($id)
            ),
            include: $this->service->model::$allowedIncludes
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     */
    public function destroy($id)
    {
        return $this
            ->service
            ->destroy($id);
    }

    public function restore(int $id): JsonResponse
    {
        $this->service->restore($id);

        return $this->okResponse(
            message: 'Restaurado com sucesso!'
        );
    }
}
