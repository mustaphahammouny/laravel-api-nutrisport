<?php

namespace App\Enums;

use App\Services\Response\Contracts\ResponseContract;
use App\Services\Response\Formats\JsonResponse;
use App\Services\Response\Formats\XmlResponse;

enum AcceptHeader: string
{
    case ApplicationJson = 'application/json';
    case ApplicationXml = 'application/xml';

    public function resolveResponse(): ResponseContract
    {
        return match ($this) {
            self::ApplicationJson => new JsonResponse,
            self::ApplicationXml => new XmlResponse,
        };
    }
}
