<?php

namespace GustavoSantarosa\LaravelToolPack;

use GustavoSantarosa\LaravelToolPack\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Model;
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
    use ApiResponse;

    protected $storeValidation;
    protected $updateValidation;
    public $model;
    protected $data;

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

        if (!$validation->authorize()) {
            $this->unauthorizedResponse('não autorizado');
        }

        if (1 == count($methodParams) && 'id' == $methodParams[0]->name) {
            Validator::validate($data, $validation->rules($currentId), $validation->messages());
        } else {
            Validator::validate($data, $validation->rules(), $validation->messages());
        }
    }

    public function index()
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
            ->when($this->data['wherenotin'] ?? null, function ($query) use ($collumns) {
                return $this->model->whereIn($query, $this->data['wherenotin'], $collumns);
            })
            ->paginate($this->data['per_page']);

        return $callback;
    }

    public function store(): model
    {
        $this->validate($this->data, 'store');

        return DB::transaction(function () {
            $callback = $this->model->create($this->data);

            foreach ($this->data as $indice => $value) {
                if (is_array($value)) {
                    $callback->$indice()->sync($value);
                }
            }

            return $callback->refresh();
        });
    }

    public function show(int $id): Model
    {
        $showed = QueryBuilder::for($this->model)
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

        if (!isset($showed)) {
            $this->notFoundResponse(data: [
                'id' => $id,
            ]);
        }

        return $showed;
    }

    public function update(int $id): Model
    {
        $this->validate($this->data, 'update', $id);

        $showed = $this->show($id);

        DB::transaction(function () use ($showed) {
            $showed->update($this->data);

            foreach ($this->data as $indice => $value) {
                if (is_array($value)) {
                    $showed->$indice()->sync($value);
                }
            }
        });

        return $showed->refresh();
    }

    public function destroy($id): ?bool
    {
        return $this->show($id)->delete();
    }
}
