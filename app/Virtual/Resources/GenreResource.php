<?php

namespace App\Virtual\Resources;

use App\Virtual\Models\Pagination;

/**
 * @OA\Schema(
 *     title="GenreResource",
 *     description="Genre resource",
 *     @OA\Xml(
 *         name="GenreResource"
 *     )
 * )
 */
class GenreResource
{
    /**
     * @OA\Property(
     *     title="State",
     *     description="State",
     *     example=true
     * )
     *
     * @var boolean
     */
    private $state;

    /**
     * @OA\Property(
     *     title="Data",
     *     description="Data"
     * )
     *
     * @var \App\Virtual\Models\Genre
     */
    private $data;
}
