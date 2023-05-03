<?php

namespace App\Http\Middleware;

use App\Traits\DecodeContentTrait;
use Closure;

class PutFormData{
    use DecodeContentTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if($request->method() == 'PUT'){
            if (preg_match('/multipart\/form-data/', $request->headers->get('Content-Type')) ||
                preg_match('/multipart\/form-data/', $request->headers->get('content-type'))){
                $rawData = $request->getContent();
                $modifyRequest = $this->decodeContent($rawData);
                $request->merge($modifyRequest->attributes->all());
                $request->files->add($modifyRequest->files->all());
            }
            $response = $next($request);
        }else {
            $response = $next($request);
        }
        return $response;
    }
}
