<?php

namespace App\Http\Resources;

class FilmCollection extends PaginatedCollection
{
    protected $resourceClass = FilmResource::class;
}
