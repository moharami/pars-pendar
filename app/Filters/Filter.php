<?php

namespace App\Filters;


use Illuminate\Database\Eloquent\Builder;

class Filter
{
    public function __construct(public  Builder $builder, public $value)
    {
    }

}