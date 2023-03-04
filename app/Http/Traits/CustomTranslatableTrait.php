<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CustomTranslatableTrait
{
    /**
     * This scope filters results by checking the translation fields.
     *
     * @param  string  $key
     * @param  string  $value
     * @param  string  $locale
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeOrWhereTranslationIlike(Builder $query, $key, $value, $locale = null)
    {
        return $query->orWhereHas('translations', function (Builder $query) use ($key, $value, $locale) {
            $query->where($this->getTranslationsTable() . '.' . $key, 'ILIKE', $value);
            if ($locale) {
                $query->where($this->getTranslationsTable() . '.' . $this->getLocaleKey(), 'ILIKE', $locale);
            }
        });
    }

    public function scopeWhereTranslationIlike(Builder $query, $key, $value, $locale = null)
    {
        return $query->whereHas('translations', function (Builder $query) use ($key, $value, $locale) {
            $query->where($this->getTranslationsTable() . '.' . $key, 'ILIKE', $value);
            if ($locale) {
                $query->where($this->getTranslationsTable() . '.' . $this->getLocaleKey(), 'ILIKE', $locale);
            }
        });
    }

    public function scopeWhereTranslationNotIlike(Builder $query, $key, $value, $locale = null)
    {
        return $query->whereHas('translations', function (Builder $query) use ($key, $value, $locale) {
            $query->where($this->getTranslationsTable() . '.' . $key, 'NOT ILIKE', $value);
            if ($locale) {
                $query->where($this->getTranslationsTable() . '.' . $this->getLocaleKey(), 'NOT ILIKE', $locale);
            }
        });
    }
}
