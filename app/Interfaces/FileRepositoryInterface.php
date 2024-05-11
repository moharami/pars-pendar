<?php

namespace App\Interfaces;

interface FileRepositoryInterface
{
    public function store($file, $directory);
}