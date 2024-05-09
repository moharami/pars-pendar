<?php

namespace App\Repositories;

use App\Interfaces\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;

class ArticleRepository implements ArticleRepositoryInterface
{

    /**
     * @return Collection
     */
    public function index($data)
    {
        return Article::filter($data)->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
      return Article::findOrFail($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return Article::create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id): mixed
    {
        return Article::whereId($id)->update($data);
    }

    /**
     * @param $id
     * @return void
     */
    public function delete($id): void
    {
        Article::destroy($id);
    }
}
