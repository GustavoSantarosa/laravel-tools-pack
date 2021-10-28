<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Relations\HasManySyncable;
use Illuminate\Database\Eloquent\Builder;

class BaseModel extends Model
{
    /**
     * Por default no laravel não possui o sync para hasmany nas relações.
     * Entao ele foi modificado, para que ele retorne o syncable.
     *
     * {@inheritDoc}
     * @return \App\Model\Relations\HasManySyncable
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return new HasManySyncable(
            $instance->newQuery(),
            $this,
            $instance->getTable() . '.' . $foreignKey,
            $localKey
        );
    }

    /**
     * Utilizado para quando o request vim com array com coluna repetida.
     * orWhere[][descricao]
     *
     * @param Builder $query
     * @param array $Where
     * @return Builder
     */
    public function arrayWhereOr(Builder $query, array $orWhere): Builder
    {
        foreach ($orWhere as $indice => $value) {
            if (is_array($value)) {
                $this->arrayWhereOr($query, $value);
            } else {
                $indice = $indice == "cpfcnpj" ? "translate({$indice}, '.,-/', '')" : $indice; //TODO Qyon Solução paliativa Temporaria
                $query->orWhereRaw("UPPER(unaccent({$indice}::text)) like UPPER(unaccent('%{$value}%'))");
            }
        }
        return $query;
    }

    /**
     * Utilizado para quando o request vim com array com coluna repetida.
     * Where[][descricao]
     *
     * @param Builder $query
     * @param array $Where
     * @return Builder
     */
    public function arrayWhere(Builder $query, array $where): Builder
    {
        foreach ($where as $indice => $value) {
            if (is_array($value)) {
                $this->arrayWhere($query, $value);
            } else {
                $indice = $indice == "cpfcnpj" ? "translate({$indice}, '.,-/', '')" : $indice; //TODO Qyon Solução paliativa Temporaria
                $query->WhereRaw("UPPER(unaccent({$indice}::text)) like UPPER(unaccent('%{$value}%'))");
            }
        }
        return $query;
    }

    public function scopeBetween(Builder $query, $column, $start, $end): Builder
    {
        return $query->where($column, ">=",Carbon::parse($start))
                    ->where($column, "<=", Carbon::parse($end));
    }
}
