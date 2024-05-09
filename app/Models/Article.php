<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory,Filterable;

    protected $fillable = ['title', 'content', 'user_id'];


    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
