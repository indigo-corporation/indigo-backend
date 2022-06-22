<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="User",
 *     description="User model",
 *     @OA\Xml(
 *         name="User"
 *     )
 * )
 */
class User
{
    /**
     * @OA\Property(
     *      title="name",
     *      description="Name",
     *      format="string",
     *      example="Pedro"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="email",
     *      description="Email",
     *      format="string",
     *      example="abc@gmail.com"
     * )
     *
     * @var string
     */
    public $email;

    /**
     * @OA\Property(
     *      title="password",
     *      description="Password",
     *      format="password",
     *      example="ADs_4688^"
     * )
     *
     * @var string
     */
    public $password;
}
