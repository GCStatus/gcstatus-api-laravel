<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Builder extends QueryBuilder
{
    /**
     * Run the query as a "select" statement against the connection.
     *
     * @return array
     */
    protected function runSelect(): array
    {
        return Cache::store('request')->remember($this->getCacheKey(), 1, function () {
            return parent::runSelect();
        });
    }

    /**
     * Returns a Unique String that can identify this Query.
     *
     * @return string
     */
    protected function getCacheKey(): string
    {
        return json_encode([
            $this->toSql() => $this->getBindings()
        ]);
    }
}
