<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UniversalDTO",
 *     type="object",
 *     title="UniversalDTO",
 *     required={},
 *     properties={
 *      @OA\Property(property="success", type="boolean"),
 *      @OA\Property(property="status", type="integer"),
 *      @OA\Property(
 *          property="error",
 *          type="object",
 *          @OA\Property(
 *              property="code",
 *              type="integer",
 *              example="10"
 *          ),
 *          @OA\Property(
 *              property="message",
 *              type="string",
 *              example="Error message"
 *          )
 *      ),
 *      @OA\Property(
 *              property="data",
 *              type="array",
 *              @OA\Items(),
 *         )
 *      }
 * )
 */

class UniversalDTO extends JsonResource{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){
        return parent::toArray($request);
    }
}
