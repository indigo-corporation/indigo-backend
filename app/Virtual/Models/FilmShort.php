<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="FilmShort",
 *     description="Film short model",
 *     @OA\Xml(
 *         name="FilmShort"
 *     )
 * )
 */
class FilmShort
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
     *      title="poster_url",
     *      description="Poster url",
     *      format="string",
     *      example="https://m.media-amazon.com/images/M/MV5BMDFkYTc0MGEtZmNhMC00ZDIzLWFmNTEtODM1ZmRlYWMwMWFmXkEyXkFqcGdeQXVyMTMxODk2OTU@._V1_SX300.jpg"
     * )
     *
     * @var string
     */
    public $poster_url;

    /**
     * @OA\Property(
     *      title="year",
     *      description="Year",
     *      format="int64",
     *      example="1994"
     * )
     *
     * @var integer
     */
    public $year;

    /**
     * @OA\Property(
     *      title="imdb_rating",
     *      description="imdb rating",
     *      format="string",
     *      example="9.0"
     * )
     *
     * @var string
     */
    public $imdb_rating;

    /**
     * @OA\Property(
     *      title="title",
     *      description="Title",
     *      format="string",
     *      example="Властелин колец: Возвращение Короля"
     * )
     *
     * @var string
     */
    public $title;
}
