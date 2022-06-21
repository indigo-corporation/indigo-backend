<?php

namespace App\Virtual\Resources;


/**
 * @OA\Schema(
 *     title="DefaultErrorResource",
 *     description="Default Error Resource",
 *     @OA\Xml(
 *         name="DefaultErrorResource"
 *     )
 * )
 */
class DefaultErrorResource
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
     * @var \App\Virtual\Models\DefaultError
     */
    private $data;
}
