<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model {
    use HasFactory;

    protected $fillable = ['author_id', 'title'];

    protected $visible = ['title', 'author', 'indices'];

    public function author() {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function indices() {
        return $this->hasMany(BookIndex::class, 'book_id')
            ->with('subindices')
            ->whereBookIndexId(null);
    }

}
