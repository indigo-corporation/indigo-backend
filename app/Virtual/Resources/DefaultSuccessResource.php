<?php

namespace App\Virtual\Resources;

/**
 * @OA\Schema(
 *     title="DefaultSuccessResource",
 *     description="Default Success Resource",
 *     @OA\Xml(
 *         name="DefaultSuccessResource"
 *     )
 * )
 */
class DefaultSuccessResource
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
     *     description="Data",
     *     example=null
     * )
     *
     * @var object
     */
    private $data;
}
