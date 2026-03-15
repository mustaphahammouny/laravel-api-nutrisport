<?php

namespace App\Services\Response\Factories;

use App\Enums\AcceptHeader;
use App\Services\Response\Contracts\ResponseContract;
use Illuminate\Http\Request;
use InvalidArgumentException;

final class ResponseFactory
{
    public function make(Request $request): ResponseContract
    {
        $acceptHeader = $request->header('Accept');

        $acceptHeader = AcceptHeader::tryFrom($acceptHeader);

        if (!$acceptHeader) {
            throw new InvalidArgumentException('Unsupported Accept Header!');
        }

        return $acceptHeader->resolveResponse();
    }
}
