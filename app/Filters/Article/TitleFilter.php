<?php

namespace App\Filters\Article;


use App\Filters\Filter;

class TitleFilter extends Filter
{
    public function apply()
    {
        return $this->builder->where('title', 'LIKE', '%'. $this->value. '%');
    }
}