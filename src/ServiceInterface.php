<?php

/**
 * Service Interface
 * php version 7.4.16
 *
 * @category Interface
 * @package  GustavoSantarosa\LaravelToolPack
 * @author   Luis Gustavo Santarosa Pinto <bolota_xd@hotmail.com>
 * @license  http://www.gnu.org/licenses/old-lic GNU
 * @link     https://github.com/GustavoSantarosa
 */

namespace GustavoSantarosa\LaravelToolPack;

use GustavoSantarosa\LaravelToolPack\DataTransferObject;

/**
 * Interface Service
 *
 * @category ServiceInterface
 * @package  GustavoSantarosa\LaravelToolPack
 * @author   Luis Gustavo Santarosa Pinto <bolota_xd@hotmail.com>
 * @license  http://www.gnu.org/licenses/old-lic GNU
 * @link     https://github.com/GustavoSantarosa
 */
interface ServiceInterface
{

    /**
     * Index
     *
     * @param $request Requisicao da rota
     * @param $model   Model Principal
     *
     * @return DataTransferObject
     */
    public function index($request, object $model): DataTransferObject;


    /**
     * Store
     *
     * @param $request Requisicao da rota
     * @param $model   Model Principal
     *
     * @return DataTransferObject
     */
    public function store($request, object $model): DataTransferObject;

    /**
     * Show
     *
     * @param $id    Identificador principal
     * @param object $model Model Principal
     *
     * @return DataTransferObject
     */
    public function show(int $id, object $model): DataTransferObject;

    /**
     * Update
     *
     * @param $request Requisicao da rota
     * @param $id      Identificador principal
     * @param $model   Model Principal
     *
     * @return DataTransferObject
     */
    public function update($request, int $id, object $model): DataTransferObject;

    /**
     * Destroy
     *
     * @param $id    Identificador principal
     * @param $model Model Principal
     *
     * @return DataTransferObject
     */
    public function destroy(int $id, object $model): DataTransferObject;

    /**
     * Status
     *
     * @param $model Model Principal
     *
     * @return DataTransferObject
     */
    public function status(object $model): DataTransferObject;
}
