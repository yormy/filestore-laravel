<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Services;

use Illuminate\Http\Request;

class LoggingHelper
{
    public static function getLogData(Request $request): array
    {
        $ipResolverClass = config('filestore.resolvers.ip');
        $ip = $ipResolverClass::get($request);

        $useragentResolverClass = config('filestore.resolvers.useragent');
        $useragent = $useragentResolverClass::get($request);

        $userResolverClass = config('filestore.resolvers.user');
        $user = $userResolverClass::get($request);

        $data = [
            'ip' => $ip,
            'useragent' => $useragent,
            'user_id' => $user?->id,
            'user_type' => $user ? get_class($user) : null,
        ];

        return $data;
    }
}
