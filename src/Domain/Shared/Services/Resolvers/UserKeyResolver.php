<?php

declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Shared\Services\Resolvers;

class UserKeyResolver
{
    public static function get($user): ?string
    {
        $tableClass = config('filestore.models.keys');
        $key = $tableClass::query()
            ->where('user_id', $user->id)
            ->where('user_type', get_class($user))
            ->get();

        return $key?->key;
    }
}
