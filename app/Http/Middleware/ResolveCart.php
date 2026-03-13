<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ResolveCart
{
    public function handle(Request $request, Closure $next): Response
    {
        $headerName = config('cart.header_name');

        $cartToken = $request->header($headerName) ?? (string) Str::uuid();

        $request->attributes->set('cart_token', $cartToken);

        $response = $next($request);

        $response->headers->set($headerName, $cartToken);

        return $response;
    }
}
