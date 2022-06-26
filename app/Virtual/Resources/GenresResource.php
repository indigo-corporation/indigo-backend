<?php

namespace App\Virtual\Resources;

use App\Virtual\Models\Pagination;

/**
 * @OA\Schema(
 *     title="GenresResource",
 *     description="Genres resource",
 *     @OA\Xml(
 *         name="GenresResource"
 *     )
 * )
 */
class GenresResource
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
     * @var \App\Virtual\Models\Genre[]
     */
    private $data;
}
