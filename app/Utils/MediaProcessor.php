<?php

namespace App\Utils;

class MediaProcessor
{

    public static function processMediaUrls($community)
    {
        $baseUrl = 'https://storage.opencomunidad.cl';

        foreach (['logo', 'banner'] as $mediaType) {
            if ($community->{$mediaType}) {
                $path = parse_url($community->{$mediaType}->url, PHP_URL_PATH);
                $community->{$mediaType}->url = $baseUrl . $path;
            }
        }
    }
}
