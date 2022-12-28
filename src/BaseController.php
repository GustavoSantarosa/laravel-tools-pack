<?php

namespace GustavoSantarosa\LaravelToolPack;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Http\Resources\DefaultCollection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use GustavoSantarosa\LaravelToolPack\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
        return $this
            ->service
            ->store();
    }

    /**
     * Atualizar.
     *
     * @param mixed $request
     * @param mixed $id
     */
    public function update($id)
    {
        return $this
            ->service
            ->update($id);
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
     * @return ReturnPrepare
     */
    public function destroy($id)
    {
        return $this
            ->service
            ->destroy($id);
    }
}
