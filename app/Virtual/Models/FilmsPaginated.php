<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="FilmsPaginated",
 *     description="FilmsPaginated",
 *     @OA\Xml(
 *         name="FilmsPaginated"
 *     )
 * )
 */
class FilmsPaginated
{
    /**
     * @OA\Property(
     *     title="Items",
     *     description="Items"
     * )
     *
     * @var \App\Virtual\Models\FilmShort[]
     */
    private $items;

    /**
     * @OA\Property(
     *     title="Pagination",
     *     description="Pagination"
     * )
     *
     * @var \App\Virtual\Models\Pagination
     */
    private $pagination;
}
