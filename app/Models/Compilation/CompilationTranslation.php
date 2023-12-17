<?php

namespace App\Models\Compilation;

use Illuminate\Database\Eloquent\Model;

class CompilationTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title'
    ];
}
