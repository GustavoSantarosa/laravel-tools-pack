<?php

namespace GustavoSantarosa\LaravelToolPack;

use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use GustavoSantarosa\LaravelToolPack\DataTransferObject;


/**
 * BaseService Classe base para as classes de serviço
 *
 */
class BaseService implements ServiceInterface
{
    /**
     * Data Transfer Object
     *
     * @var DataTransferObject
     */
    protected $dto;

    public function __construct()
    {
        $this->dto = new DataTransferObject();
    }

    public function index($request, $model)
    {
        $callback = QueryBuilder::for($model)
            ->allowedFields($model::$allowedFields)
            ->allowedIncludes($model::$allowedIncludes)
            ->allowedFilters([
                AllowedFilter::scope('between'),
            ])
            ->where(function ($query) use ($request, $model) {
                if (isset($request->where)) {
                    $model->arrayWhere($query, $request->where);
                }
                return $query;
            })
            ->orWhere(function ($query) use ($request, $model) {
                if (isset($request->orwhere)) {
                    $model->arrayWhereOr($query, $request->orwhere);
                }

                return $query;
            })
            ->paginate($request->per_page)
            ->toarray();

        $this->dto->setSuccess(true);
        $this->dto->setIndex(true);
        $this->dto->setMessage("{$model::$title} listado com sucesso!");
        $this->dto->setInclude($model::$allowedIncludes);
        $this->dto->setData($callback);
        return $this->dto;
    }

    public function store($request, $model)
    {
        DB::connection('pgsql_erp')->beginTransaction();

        $callback = ($model)->create((array) $request);

        foreach ($request as $indice => $value) {
            if (is_array($value)) {
                $callback->$indice()->sync($value);
            }
        }

        $this->show($callback->id, $model);

        DB::connection('pgsql_erp')->commit();

        $this->dto->setSuccess(true);
        $this->dto->setMessage("{$model::$title} de id '{$this->dto->getData()->id}' inserido com sucesso!");
        return $this->dto;
    }

    public function show($id, $model)
    {
        $callback = QueryBuilder::for($model)
            ->allowedFields($model::$allowedFields)
            ->allowedIncludes($model::$allowedIncludes)
            ->find($id);

        if (!isset($callback)) {
            $this->dto->setSuccess(false);
            $this->dto->setMessage("{$model::$title} de id '{$id}' não encontrado!");
            return $this->dto;
        }
        $this->dto->setSuccess(true);
        $this->dto->setMessage("{$model::$title} de id '{$id}' encontrado!");
        $this->dto->setInclude($model::$allowedIncludes);
        $this->dto->setData($callback);

        return $this->dto;
    }

    public function update($request, $id, $model)
    {
        $this->show($id, $model);

        if (!$this->dto->getSuccess()) {
            return $this->dto;
        }

        DB::connection('pgsql_erp')->beginTransaction();

        $this->dto->getData()->update($request);

        foreach ($request as $indice => $value) {
            if (is_array($value)) {
                $this->dto->getData()->$indice()->sync($value);
            }
        }

        $this->show($id, $model);

        DB::connection('pgsql_erp')->commit();

        $this->dto->setSuccess(true);
        $this->dto->setMessage("{$model::$title} de id '{$id}' alterado com sucesso!");
        return $this->dto;
    }

    public function destroy($id, $model)
    {
        $this->show($id, $model);

        if (!$this->dto->getSuccess()) {
            return $this->dto;
        }

        DB::connection('pgsql_erp')->beginTransaction();

        $this->dto->getData()->destroy($id);

        DB::connection('pgsql_erp')->commit();

        $this->dto->setSuccess(true);
        $this->dto->setMessage("{$model::$title} de id '{$id}' excluido com sucesso!");
        return $this->dto;
    }
}
