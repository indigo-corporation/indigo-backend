<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="AccessToken",
 *     description="AccessToken",
 *     @OA\Xml(
 *         name="AccessToken"
 *     )
 * )
 */
class AccessToken
{

    /**
     * @OA\Property(
     *      title="access_token",
     *      description="Access_token",
     *      format="string",
     *      example="2|sbMBmoXZf9tDVk5fOSLRWbDdozAfnjPQ4g6HOSSE"
     * )
     *
     * @var string
     */
    public $access_token;
}
