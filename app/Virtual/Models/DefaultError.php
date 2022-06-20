<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="DefaultError",
 *     description="DefaultError model",
 *     @OA\Xml(
 *         name="DefaultError"
 *     )
 * )
 */
class DefaultError
{

    /**
     * @OA\Property(
     *      title="code",
     *      description="Error code",
     *      format="int64",
     *      example=100
     * )
     *
     * @var integer
     */
    public $code;

    /**
     * @OA\Property(
     *      title="message",
     *      description="Error message",
     *      format="string",
     *      example="Some error message"
     * )
     *
     * @var string
     */
    public $message;
}
