<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="Pagination",
 *     description="Pagination model",
 *     @OA\Xml(
 *         name="Pagination"
 *     )
 * )
 */
class Pagination
{
    /**
     * @OA\Property(
     *      title="total",
     *      description="Total",
     *      format="int64",
     *      example=391
     * )
     *
     * @var integer
     */
    public $total;

    /**
     * @OA\Property(
     *      title="count",
     *      description="Count",
     *      format="int64",
     *      example=20
     * )
     *
     * @var integer
     */
    public $count;

    /**
     * @OA\Property(
     *      title="per_page",
     *      description="Per page",
     *      format="int64",
     *      example=20
     * )
     *
     * @var integer
     */
    public $per_page;

    /**
     * @OA\Property(
     *      title="current_page",
     *      description="Current page",
     *      format="int64",
     *      example=2
     * )
     *
     * @var integer
     */
    public $current_page;

    /**
     * @OA\Property(
     *      title="total_pages",
     *      description="Total pages",
     *      format="int64",
     *      example=20
     * )
     *
     * @var integer
     */
    public $total_pages;
}
