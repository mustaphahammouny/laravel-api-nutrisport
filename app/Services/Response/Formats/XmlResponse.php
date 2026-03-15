<?php

namespace App\Services\Response\Formats;

use App\Enums\AcceptHeader;
use App\Services\Response\Contracts\ResponseContract;
use Spatie\ArrayToXml\ArrayToXml;
use Symfony\Component\HttpFoundation\Response;

class XmlResponse implements ResponseContract
{
    public function toResponse(array $data): Response
    {
        $xml = ArrayToXml::convert($data, [], true, 'UTF-8', '1.1');

        return response($xml)->withHeaders([
            'Content-Type' => AcceptHeader::ApplicationXml->value,
        ]);
    }
}
