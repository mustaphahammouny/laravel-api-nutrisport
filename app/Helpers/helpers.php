<?php

use App\Models\Site;

if (! function_exists('current_site')) {
    function current_site(): ?Site
    {
        return app()->bound(Site::class) ? app(Site::class) : null;
    }
}
