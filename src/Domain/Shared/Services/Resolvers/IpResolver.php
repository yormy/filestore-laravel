<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Services\Resolvers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

class IpResolver
{
    public static function get(?Request $request): array|string|null
    {
        if (! $request) {
            return request()->ip();
        }

        if ($cloudflarePassthroughIp = $request->header('CF_CONNECTING_IP')) {
            return $cloudflarePassthroughIp;
        }

        return $request->ip();
    }

    public static function currentMatches(?Request $request, array $list): bool
    {
        $ip = self::get($request);

        return self::matches($ip, $list);
    }

    public static function matches(string $ip, array $list): bool
    {
        if (IpUtils::checkIp($ip, $list)) {
            return true;
        }

        return false;
    }
}
