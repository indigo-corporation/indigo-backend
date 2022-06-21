<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="Country",
 *     description="Country model",
 *     @OA\Xml(
 *         name="Country"
 *     )
 * )
 */
class Country
{
    /**
     * @OA\Property(
     *      title="id",
     *      description="ID",
     *      format="int64",
     *      example=101
     * )
     *
     * @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *      title="code",
     *      description="code",
     *      format="string",
     *      example="AD"
     * )
     *
     * @var string
     */
    public $code;

    /**
     * @OA\Property(
     *      title="name",
     *      description="Name",
     *      format="string",
     *      example="Andorra"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="title",
     *      description="Title",
     *      format="string",
     *      example="Андорра"
     * )
     *
     * @var string
     */
    public $title;
}
