<?php

namespace App\Virtual\Resources;

use App\Virtual\Models\Pagination;

/**
 * @OA\Schema(
 *     title="FilmsResource",
 *     description="Films resource",
 *     @OA\Xml(
 *         name="FilmsResource"
 *     )
 * )
 */
class FilmsResource
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
     * @var \App\Virtual\Models\Film[]
     */
    private $data;

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
