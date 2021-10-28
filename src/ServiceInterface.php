<?php

namespace App\Services;

use GustavoSantarosa\LaravelToolPack\DataTransferObject;

/**
 * ServiceInterface Interface para as classes de serviço
 *
 */
interface ServiceInterface
{
        
    /**
     * index
     *
     * @return DataTransferObject
     */
    public function index($request, $model);
        
        
    /**
     * store
     *
     * @param  mixed $request
     * @return DataTransferObject
     */
    public function store($request, $model);
    
    /**
     * show
     *
     * @param  mixed $id
     * @return DataTransferObject
     */
    public function show($id, $model);
    
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return DataTransferObject
     */
    public function update($request, $id, $model);
        
    /**
     * destroy
     *
     * @param  mixed $id
     * @return DataTransferObject
     */
    public function destroy($id, $model);
}