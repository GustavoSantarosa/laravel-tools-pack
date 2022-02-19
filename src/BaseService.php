<?php

namespace GustavoSantarosa\LaravelToolPack;

use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use GustavoSantarosa\LaravelToolPack\DatabaseTrait;
use GustavoSantarosa\LaravelToolPack\ServiceInterface;
use GustavoSantarosa\LaravelToolPack\DataTransferObject;


/**
 * BaseService Classe base para as classes de serviço
 *
 */
class BaseService implements ServiceInterface
{
    use DatabaseTrait;

    public function index($request, object $model): DataTransferObject
    {
        $collumns = $this->getColumnListing($model->getTable());

        $callback = QueryBuilder::for($model)
            ->allowedFields($collumns)
            ->allowedIncludes($model::$allowedIncludes)
            ->allowedSorts($collumns)
            ->allowedFilters([
                AllowedFilter::scope('between'),
                AllowedFilter::scope('date'),
            ])
            ->where(
                function ($query) use ($request, $model) {
                    if (isset($request->where)) {
                        $model->arrayWhere($query, $request->where);
                    }
                    return $query;
                }
            )
            ->where(
                function ($query) use ($request, $model) {
                    if (isset($request->orwhere)) {
                        $model->arrayWhereOr($query, $request->orwhere);
                    }

                    return $query;
                }
            )
            ->paginate($request->per_page)
            ->toarray();

        $indexDto = new DataTransferObject();

        $indexDto->setSuccess(true);
        $indexDto->setIndex(true);
        $indexDto->setMessage("{$model::$title} listado com sucesso!");
        $indexDto->setInclude($model::$allowedIncludes);
        $indexDto->setData($callback);
        return $indexDto;
    }

    public function store($request, object $model): DataTransferObject
    {
        DB::connection('pgsql_erp')->beginTransaction();

        $callback = ($model)->create((array) $request);

        foreach ($request as $indice => $value) {
            if (is_array($value)) {
                $callback->$indice()->sync($value);
            }
        }

        $storeDto = $this->show($callback->id, $model);

        DB::connection('pgsql_erp')->commit();

        $storeDto->successMessage("{$model::$title} de id '{$storeDto->getData()->id}' inserido com sucesso!");
        return $storeDto;
    }

    public function show(int $id, object $model): DataTransferObject
    {
        $showDto = new DataTransferObject();

        $callback = QueryBuilder::for($model)
            ->allowedFields($model::$allowedFields)
            ->allowedIncludes($model::$allowedIncludes)
            ->find($id);

        if (!isset($callback)) {
            $showDto->errorMessage("{$model::$title} de id '{$id}' não encontrado!");
            return $showDto;
        }

        $showDto->successMessage("{$model::$title} de id '{$id}' não encontrado!", $callback, $model::$allowedIncludes);
        return $showDto;
    }

    public function update($request, int $id, object $model): DataTransferObject
    {
        $updateDto = $this->show($id, $model);

        if (!$updateDto->getSuccess()) {
            return $updateDto;
        }

        DB::connection('pgsql_erp')->beginTransaction();

        $updateDto->getData()->update($request);

        foreach ($request as $indice => $value) {
            if (is_array($value)) {
                $updateDto->getData()->$indice()->sync($value);
            }
        }

        $updateDto = $this->show($id, $model);

        DB::connection('pgsql_erp')->commit();

        $updateDto->successMessage("{$model::$title} de id '{$id}' alterado com sucesso!");
        return $updateDto;
    }

    public function destroy($id, $model): DataTransferObject
    {

        $destroyDto = $this->show($id, $model);

        if (!$destroyDto->getSuccess()) {
            return $destroyDto;
        }

        DB::connection('pgsql_erp')->beginTransaction();

        $destroyDto->getData()->destroy($id);

        DB::connection('pgsql_erp')->commit();

        $destroyDto->successMessage("{$model::$title} de id '{$id}' excluido com sucesso!");
        return $destroyDto;
    }

    public function status(object $model): DataTransferObject
    {
        $statusDto = new DataTransferObject();

        $statusDto->successMessage("Status da tabela {$model::$title} localizado com sucesso!");
        return $statusDto;
    }
}
