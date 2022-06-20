<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="Film",
 *     description="Film model",
 *     @OA\Xml(
 *         name="Film"
 *     )
 * )
 */
class Film
{
    /**
     * @OA\Property(
     *      title="id",
     *      description="ID",
     *      format="int64",
     *      example=3453
     * )
     *
     * @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *      title="original_title",
     *      description="Original title",
     *      format="string",
     *      example="The Shawshank Redemption"
     * )
     *
     * @var string
     */
    public $original_title;

    /**
     * @OA\Property(
     *      title="original_language",
     *      description="Original language",
     *      format="string",
     *      example="en"
     * )
     *
     * @var string
     */
    public $original_language;
}
