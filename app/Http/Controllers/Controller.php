<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// $this->middleware error: https://jel-log.tistory.com/43
/**
 * @OA\Info(
 *     title="Open API Swagger",
 *     version="1.0.0",
 *     description="API 스펙 명세",
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="laravel api server"
 * )
 */
abstract class Controller extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;
}
