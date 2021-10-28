<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Qyon\ServiceLayer\ReturnPrepare;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Services\ServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * service
     *
     * @var ServiceInterface
     */
    protected $service;
    protected $model;

    public function __construct(ServiceInterface $service, $model)
    {
        $this->service = $service;
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     * @return ReturnPrepare
     */
    public function index(Request $request)
    {
        if (!isset($request->per_page)) {
            $request->per_page = 50;
        }

        //return response()->json($this->service->index($request),200);
        return ReturnPrepare::getMessageDTO($this->service->index($request, $this->model), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return ReturnPrepare
     */
    public function show($id)
    {
        return ReturnPrepare::getMessageDTO($this->service->show($id, $this->model), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return ReturnPrepare
     */
    public function destroy($id)
    {
        return ReturnPrepare::getMessageDTO($this->service->destroy($id, $this->model), 200);
    }
}
