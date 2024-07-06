<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookIndex extends Model {
    use HasFactory;

    protected $fillable = ['book_id', 'book_index_id', 'title', 'page'];

    protected $visible = ['id', 'title', 'page', 'subindices'];

    public function subindices() {
        return $this->hasMany(BookIndex::class, 'book_index_id')
            ->with('subindices');
    }

}
