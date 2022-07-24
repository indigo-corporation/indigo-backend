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
     *      title="runtime",
     *      description="Runtime",
     *      format="int64",
     *      example="128"
     * )
     *
     * @var integer
     */
    public $runtime;

    /**
     * @OA\Property(
     *      title="release_date",
     *      description="Release date",
     *      format="date",
     *      example="2022-06-25"
     * )
     *
     * @var
     */
    public $release_date;

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
     *      title="imdb_id",
     *      description="imdb ID",
     *      format="string",
     *      example="tt0167260"
     * )
     *
     * @var string
     */
    public $imdb_id;

    /**
     * @OA\Property(
     *      title="imdb_rating",
     *      description="imdb rating",
     *      type="number",
     *      format="double",
     *      example=9.1
     * )
     *
     * @var string
     */
    public $imdb_rating;

    /**
     * @OA\Property(
     *      title="shiki_id",
     *      description="shiki ID",
     *      format="string",
     *      example="16720"
     * )
     *
     * @var string
     */
    public $shiki_id;

    /**
     * @OA\Property(
     *      title="shiki_rating",
     *      description="shiki rating",
     *      type="number",
     *      format="double",
     *      example=9.1
     * )
     *
     * @var string
     */
    public $shiki_rating;

    /**
     * @OA\Property(
     *      title="is_anime",
     *      description="is anime",
     *      format="boolean",
     *      example=false
     * )
     *
     * @var string
     */
    public $is_anime;

    /**
     * @OA\Property(
     *      title="is_serial",
     *      description="is serial",
     *      format="boolean",
     *      example=false
     * )
     *
     * @var string
     */
    public $is_serial;

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

    /**
     * @OA\Property(
     *      title="overview",
     *      description="Overview",
     *      format="string",
     *      example="Последняя часть трилогии о Кольце Всевластия и о героях, взявших на себя бремя спасения Средиземья. Повелитель сил Тьмы Саурон направляет свои бесчисленные рати под стены Минас-Тирита, крепости Последней Надежды. Он предвкушает близкую победу, но именно это и мешает ему заметить две крохотные фигурки — хоббитов, приближающихся к Роковой Горе, где им предстоит уничтожить Кольцо Всевластия. Улыбнется ли им счастье?"
     * )
     *
     * @var string
     */
    public $overview;

    /**
     * @OA\Property(
     *      title="genres",
     *      description="Genres",
     * )
     *
     * @var Genre[]
     */
    public $genres;

    /**
     * @OA\Property(
     *      title="countries",
     *      description="Countries",
     * )
     *
     * @var Country[]
     */
    public $countries;
}
