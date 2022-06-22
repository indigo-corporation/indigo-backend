<?php

namespace App\Virtual\Requests;

/**
 * @OA\Schema(
 *      title="Register request",
 *      description="Register request body data",
 *      type="object",
 *      required={"*"}
 * )
 */
class RegisterRequest
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
