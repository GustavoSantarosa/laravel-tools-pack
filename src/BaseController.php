<?php

namespace GustavoSantarosa\LaravelToolPack;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use GustavoSantarosa\LaravelToolPack\ReturnPrepare;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BaseController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected $service;
    protected $model;

    public function __construct(
        $service,
    ) {
        $this->service  = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return ReturnPrepare
     */
    public function index()
    {
        return $this
            ->service
            ->index()
            ->getMessageDTO();
    }

    /**
     * Store the specified resource from storage.
     *
     * @param  Request $request
     */
    public function store()
    {
        return $this
            ->service
            ->store()
            ->getMessageDTO();
    }

    /**
     * Atualizar
     *
     * @param  mixed $request
     * @param  mixed $id
     */
    public function update($id)
    {
        return $this
            ->service
            ->update($id)
            ->getMessageDTO();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return ReturnPrepare
     */
    public function show($id)
    {
        return $this
            ->service
            ->show($id)
            ->getMessageDTO();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return ReturnPrepare
     */
    public function destroy($id)
    {
        return $this
            ->service
            ->destroy($id)
            ->getMessageDTO();
    }
}
