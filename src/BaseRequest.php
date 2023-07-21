<?php

namespace App\Http\Requests;

use App\Models\Erp\Billing;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    protected $ruleConfig;
    protected $model;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $rule = (new Billing())
            ->byBetweenAndRule($this->ruleConfig, Carbon::now()->toDateString())
            ->first();

        $totalAmount = config($this->ruleConfig) + (null !== $rule ? $rule->contracted_quantity - $rule->used_quantity : 0);

        return $totalAmount <= $this->model::count() ? false : true;
    }
}
