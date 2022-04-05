<?php

namespace GustavoSantarosa\LaravelToolPack;

use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use GustavoSantarosa\LaravelToolPack\DatabaseTrait;
use GustavoSantarosa\LaravelToolPack\DataTransferObject;


/**
 * BaseService Classe base para as classes de serviço
 *
 */
class BaseService
{
    use DatabaseTrait;

    protected $storeValidation;
    protected $updateValidation;
    protected $model;

    public function __construct(
        ?object $storeValidation = null,
        ?object $updateValidation = null,
        ?object $model = null,
        ?array $data = []
    ) {
        $this->storeValidation = $storeValidation;
        $this->updateValidation = $updateValidation;
        $this->model = $model;
        $this->data  = $data;
    }

    /**
     * Valida os dados
     *
     * @param [Array] $data
     * @param string $caller Upper Case Http method
     * @return void
     */
    public function validate(
        $data,
        $caller = null,
        $currentId = null,
        $customValidation = null
    ) {
        if ($customValidation) {
            $validation = $customValidation;
        } else {
            switch ($caller) {
                case 'store':
                    $validation = $this->storeValidation;
                    break;
                case 'update':
                    $validation = $this->updateValidation;
                    break;
                default:
                    return;
            }
        }

        if (!$validation) {
            return;
        }

        //Checks if the validate method have a ID param and, if necessary, sends it
        $method = new \ReflectionMethod(get_class($validation), 'rules');
        $methodParams = $method->getParameters();

        // TODO Qyon: $validation->authorize();

        if ((count($methodParams) == 1 && $methodParams[0]->name == 'id')) {
            Validator::validate($data, $validation->rules($currentId), $validation->messages());
        } else {
            Validator::validate($data, $validation->rules(), $validation->messages());
        }
    }


    public function index(): DataTransferObject
    {
        if (!isset($this->data['per_page'])) {
            $this->data['per_page'] = 50;
        }

        $collumns = $this->getColumnListing($this->model->getTable());

        $callback = QueryBuilder::for($this->model)
            ->allowedFields($collumns)
            ->allowedIncludes($this->model::$allowedIncludes)
            ->allowedSorts($collumns)
            ->allowedFilters([
                AllowedFilter::scope('between'),
                AllowedFilter::scope('date'),
            ])
            ->where(
                function ($query) use ($request) {
                    if (isset($request->where)) {
                        $this->model->arrayWhere($query, $request->where);
                    }
                    return $query;
                }
            )
            ->where(
                function ($query) use ($request) {
                    if (isset($request->orwhere)) {
                        $this->model->arrayWhereOr($query, $request->orwhere);
                    }

                    return $query;
                }
            )
            ->paginate($request->per_page)
            ->toarray();

        $indexDto = new DataTransferObject();

        $indexDto->setSuccess(true);
        $indexDto->setIndex(true);
        $indexDto->setMessage("Successfully found!");
        $indexDto->setInclude($this->model::$allowedIncludes);
        $indexDto->setData($callback);
        return $indexDto;
    }

    public function store($request): DataTransferObject
    {
        $this->validate($this->data, 'store');

        DB::connection('pgsql_erp')->beginTransaction();

        $callback = ($this->model)->create((array) $request);

        foreach ($request as $indice => $value) {
            if (is_array($value)) {
                $callback->$indice()->sync($value);
            }
        }

        $storeDto = $this->show($callback->id, $this->model);

        DB::connection('pgsql_erp')->commit();

        $storeDto->successMessage("Successfully created!");
        return $storeDto;
    }

    public function show(int $id): DataTransferObject
    {
        $showDto = new DataTransferObject();

        $callback = QueryBuilder::for($this->model)
            ->allowedFields($this->model::$allowedFields)
            ->allowedIncludes($this->model::$allowedIncludes)
            ->find($id);

        if (!isset($callback)) {
            $showDto->errorMessage("{$this->model::$title} de id '{$id}' não encontrado!");
            return $showDto;
        }

        $showDto->successMessage("Successfully found!", $callback, $this->model::$allowedIncludes);
        return $showDto;
    }

    public function update($request, int $id): DataTransferObject
    {
        $this->validate($this->data, 'update', $id);

        $updateDto = $this->show($id, $this->model);

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

        $updateDto = $this->show($id, $this->model);

        DB::connection('pgsql_erp')->commit();

        $updateDto->successMessage("Successfully updated!");
        return $updateDto;
    }

    public function destroy($id): DataTransferObject
    {

        $destroyDto = $this->show($id, $this->model);

        if (!$destroyDto->getSuccess()) {
            return $destroyDto;
        }

        DB::connection('pgsql_erp')->beginTransaction();

        $destroyDto->getData()->destroy($id);

        DB::connection('pgsql_erp')->commit();

        $destroyDto->successMessage("Successfully deleted!");
        return $destroyDto;
    }

    public function status(): DataTransferObject
    {
        $statusDto = new DataTransferObject();

        $statusDto->successMessage("Successfully found!");
        return $statusDto;
    }
}
