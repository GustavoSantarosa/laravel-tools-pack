<?php

namespace GustavoSantarosa\LaravelToolPack;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * BaseService Classe base para as classes de serviço.
 */
class BaseService
{
    use DatabaseTrait;

    protected $storeValidation;
    protected $updateValidation;
    protected $model;

    public function __construct(
        array $data,
        object $model,
        ?object $storeValidation = null,
        ?object $updateValidation = null
    ) {
        $this->storeValidation  = $storeValidation;
        $this->updateValidation = $updateValidation;
        $this->model            = $model;
        $this->data             = $data;
    }

    /**
     * Valida os dados.
     *
     * @param [Array] $data
     * @param string $caller Upper Case Http method
     *
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

        // Checks if the validate method have a ID param and, if necessary, sends it
        $method       = new \ReflectionMethod(get_class($validation), 'rules');
        $methodParams = $method->getParameters();

        // TODO Qyon: $validation->authorize();

        if (1 == count($methodParams) && 'id' == $methodParams[0]->name) {
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
                function ($query) {
                    if (isset($this->data['where'])) {
                        $this->model->arrayWhere($query, $this->data['where']);
                    }

                    return $query;
                }
            )
            ->orwhere(
                function ($query) {
                    if (isset($this->data['orwhere'])) {
                        $this->model->arrayWhereOr($query, $this->data['orwhere']);
                    }

                    return $query;
                }
            )
            ->when($this->data['wherein'] ?? null, function ($query) use ($collumns) {
                return $this->model->whereIn($query, $this->data['wherein'], $collumns);
            })
            ->paginate($this->data['per_page'])
            ->toarray();

        $indexDto = new DataTransferObject();
        $indexDto->successMessage(
            __('messages.successfully.show'),
            $callback,
            $this->model::$allowedIncludes
        );

        $indexDto->setIndex(true);

        return $indexDto;
    }

    public function store(): DataTransferObject
    {
        $this->validate($this->data, 'store');

        DB::beginTransaction();

        $callback = $this->model->create($this->data);

        foreach ($this->data as $indice => $value) {
            if (is_array($value)) {
                $callback->$indice()->sync($value);
            }
        }

        $storeDto = $this->show($callback->id, $this->model);

        DB::commit();

        $storeDto->setMessage(__('messages.successfully.created'));

        return $storeDto;
    }

    public function show(int $id): DataTransferObject
    {
        $showDto = new DataTransferObject();

        $callback = QueryBuilder::for($this->model)
            ->allowedFields($this->model::$allowedFields)
            ->allowedIncludes($this->model::$allowedIncludes)
            ->where(
                function ($query) {
                    if (isset($this->data['where'])) {
                        $this->model->arrayWhere($query, $this->data['where']);
                    }

                    return $query;
                }
            )
            ->orwhere(
                function ($query) {
                    if (isset($this->data['orwhere'])) {
                        $this->model->arrayWhereOr($query, $this->data['orwhere']);
                    }

                    return $query;
                }
            )
            ->find($id);

        if (!isset($callback)) {
            $showDto->errorMessage(__('messages.errors.notfound', [
                'id' => $id,
            ]));

            return $showDto;
        }

        $showDto->successMessage(
            __('messages.successfully.show'),
            $callback,
            $this->model::$allowedIncludes
        );

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

        $updateDto->successMessage(__('messages.successfully.updated'));

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

        $destroyDto->successMessage(__('messages.successfully.deleted'));

        return $destroyDto;
    }

    public function status(): DataTransferObject
    {
        $statusDto = new DataTransferObject();

        $statusDto->successMessage(__('messages.successfully.show'));

        return $statusDto;
    }
}
