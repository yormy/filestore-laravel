<?php declare(strict_types=1);

namespace Yormy\FilestoreLaravel\Domain\Download\Services;

use Illuminate\Support\Facades\Auth;
use Yormy\FilestoreLaravel\Domain\Shared\Enums\FileEncryptionExtension;
use Yormy\FilestoreLaravel\Domain\Shared\Models\FilestoreFile;
use Yormy\FilestoreLaravel\Exceptions\InvalidVariantException;

abstract class FileBase
{
    protected static function getFilename(?string $variant, FilestoreFile $fileRecord): string
    {
        $filename = $fileRecord->getFullPath();
        $useVariant = null;

        if (isset($variant)) {
            $useVariant = self::findVariant($variant, $fileRecord);
        }

        if ($useVariant) {
            $filename = $fileRecord->getFullPath($useVariant['filename']);
        }

        return $filename;
    }

    protected static function findVariant(string $selectedVariant, FilestoreFile $file)
    {
        $existingVariants = json_decode($file->variants, true);

        if (! $existingVariants) {
            throw new InvalidVariantException;
        }

        $useVariant = null;
        foreach ($existingVariants as $key => $variant) {
            if ($variant['name'] === $selectedVariant) {
                $useVariant = $existingVariants[$key];
            }
        }

        if (! $useVariant) {
            throw new InvalidVariantException;
        }

        return $useVariant;
    }

    protected static function isEncrypted(string $fullPath): bool
    {
        $pathinfo = pathinfo($fullPath);
        $encryptedExtensions = FileEncryptionExtension::getAll();

        if (isset($pathinfo['extension']) && (in_array('.'.$pathinfo['extension'], $encryptedExtensions))) {
            return true;
        }

        return false;
    }

    protected static function getKey($fileRecord, $user = null)
    {
        $encryptionKey = null;
        if ($fileRecord->user_encryption) {
            $userKeyResolverClass = config('filestore.resolvers.user_key_resolver');
            $userKeyResolver = new $userKeyResolverClass;

            if(!$user) {
                $user = auth::user();
            }

            $userKey = $userKeyResolver->get($user);
            $encryptionKey = $userKey;
        }

        return $encryptionKey;
    }
}
