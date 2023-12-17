<?php

namespace App\Models\Compilation;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Compilation\CompilationTranslation
 *
 * @property int $id
 * @property int $compilation_id
 * @property string $locale
 * @property string $title
 * @method static \Illuminate\Database\Eloquent\Builder|CompilationTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompilationTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CompilationTranslation query()
 * @mixin \Eloquent
 */
class CompilationTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title'
    ];
}
