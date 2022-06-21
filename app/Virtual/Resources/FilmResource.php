<?php

namespace App\Virtual\Resources;

/**
 * @OA\Schema(
 *     title="FilmResource",
 *     description="Film resource",
 *     @OA\Xml(
 *         name="FilmResource"
 *     )
 * )
 */
class FilmResource
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
     * @var \App\Virtual\Models\Film
     */
    private $data;
}
