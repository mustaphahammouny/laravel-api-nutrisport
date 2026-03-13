<?php

return [
    'cache_ttl' => env('CART_CACHE_TTL', 3 * 24 * 60 * 60),
    'header_name' => env('CART_HEADER_NAME', 'X-Cart-Token'),
];
