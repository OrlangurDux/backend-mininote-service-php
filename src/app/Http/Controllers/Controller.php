<?php

namespace App\Http\Controllers;

use App\Http\Resources\UniversalDTO;
use Laravel\Lumen\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * Class Controller
 * @package App\Http\Controllers
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="0.1.0",
 *         title="Mini Note RESTful API",
 *         @OA\License(name="Apache")
 *     ),
 *     @OA\Server(
 *         description="API server development",
 *         url="http://localhost:2880/api/v1",
 *     ),
 *     @OA\Server(
 *         description="API server production",
 *         url="https://mininote.orlangur.link/api/v1",
 *     )
 * )
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * )
 */
class Controller extends BaseController{
    //
    /**
     * @OA\Get(
     *     path="/version",
     *     summary="Version",
     *     operationId="version",
     *     tags={"Help"},
     *     description="Version",
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UniversalDTO")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="unexpected error",
     *         @OA\Schema(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getVersion(): UniversalDTO{
        $version = [
            'version' => config('app.version'),
            'author' => 'Alexey (Orlangur)',
            'contact' => 'o@orlangur.link'
        ];
        return (new UniversalDTO($version))->additional(['success' => true, 'status' => 200]);
    }
}
