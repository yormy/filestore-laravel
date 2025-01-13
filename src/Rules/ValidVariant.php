<?php

namespace Yormy\FilestoreLaravel\Rules;

use Yormy\FilestoreLaravel\Domain\Upload\Observers\Events\FileDownloadWrongVariantEvent;
use Yormy\FilestoreLaravel\Exceptions\InvalidValueException;

class ValidVariant
{
    public static function validate(?string $variant): void
    {
        if ($variant && ! array_key_exists($variant, config('filestore.variants'))) {
            event(new FileDownloadWrongVariantEvent($variant));
            throw new InvalidValueException('Variant not allowed');
        }
    }
}
