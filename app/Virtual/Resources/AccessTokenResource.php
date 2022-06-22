<?php

namespace App\Virtual\Resources;


/**
 * @OA\Schema(
 *     title="AccessTokenResource",
 *     description="Access token resource",
 *     @OA\Xml(
 *         name="AccessTokenResource"
 *     )
 * )
 */
class AccessTokenResource
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
     * @var \App\Virtual\Models\AccessToken
     */
    private $data;
}
