<?php

namespace App\Traits;

use App\Filters\FilterQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    public function scopeFilter(Builder $query, Request $request)
    {
        $filter = new FilterQuery($query, $request);
        return $filter->apply();
    }
}