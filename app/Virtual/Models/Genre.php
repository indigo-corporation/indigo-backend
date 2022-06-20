<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="Genre",
 *     description="Genre model",
 *     @OA\Xml(
 *         name="Genre"
 *     )
 * )
 */
class Genre
{
    /**
     * @OA\Property(
     *      title="id",
     *      description="ID",
     *      format="int64",
     *      example=6
     * )
     *
     * @var integer
     */
    public $id;

    /**
     * @OA\Property(
     *      title="name",
     *      description="Name",
     *      format="string",
     *      example="animation"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="slug",
     *      description="Slug",
     *      format="string",
     *      example="animation"
     * )
     *
     * @var string
     */
    public $slug;

    /**
     * @OA\Property(
     *      title="title",
     *      description="Title",
     *      format="string",
     *      example="анимация"
     * )
     *
     * @var string
     */
    public $title;
}
