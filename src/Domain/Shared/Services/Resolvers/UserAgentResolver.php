<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Services\Resolvers;

use Jenssegers\Agent\Agent;

class UserAgentResolver
{
    public static function get(): string
    {
        $userAgent = '';

        $agent = new Agent();
        $platform = $agent->platform();

        $versionPlatform = $agent->version($platform);
        if ($versionPlatform) {
            $userAgent = $platform.' '.$versionPlatform;
        }

        $browser = $agent->browser();

        if ($browser) {
            $browserVersion = $agent->version($browser);
            $userAgent .= $browser;
            if ($browserVersion) {
                $userAgent .= ' '.$browserVersion;
            }
        }

        return $userAgent;
    }
}
