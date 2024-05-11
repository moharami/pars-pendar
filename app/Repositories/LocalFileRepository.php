<?php

namespace App\Repositories;

use App\Interfaces\FileRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class LocalFileRepository implements FileRepositoryInterface
{
    public function store($file, $directory)
    {
        return $file->store($directory, 'public');
    }

    public function delete($filePath)
    {
        Storage::disk('public')->delete($filePath);
    }
}
