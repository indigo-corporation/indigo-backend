<?php

namespace App\Http\ApiControllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 *
 * @OA\Info(
 *   title="Indigo API",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="support@example.com"
 *   )
 * )
 *
 * @OA\Server(
 *      url="https://indigo-films.herokuapp.com/api/",
 *      description="API Server"
 * )
 *
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentification"
 * )
 *
 * @OA\Tag(
 *     name="Films",
 *     description="Films"
 * )
 * */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
