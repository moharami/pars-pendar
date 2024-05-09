<?php

namespace App\Filters;


use App\Exceptions\FailedToFindFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FilterQuery
{
    private string $filterNamespace;

    public function __construct(public Builder $query, public Request $request)
    {
        $this->filterNamespace = 'App\Filters\\' . class_basename($this->query->getModel()) . '\\';
    }


    public function apply()
    {
        foreach ($this->request->all() as $item => $value) {
            if (is_null($value)) {
                continue;
            }
            $class = $this->filterNamespace . Str::studly($item) . 'Filter';

            throw_if(!class_exists($class), FailedToFindFilter::class, $item);

            $this->query = (new $class($this->query, $value))->apply();
        }

        return $this->query;
    }
}