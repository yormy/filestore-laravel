<?php
namespace Yormy\FilestoreLaravel\Domain\Shared\Enums;

enum FileEncryptionExtension: string
{
    case SYSTEM = '.xfile';

    case USER = '.xufile';

    case SYSTEMUSER = '.x2file';

    case USERSUPLIED = '.xsfile';

    case PASSPHRASE = '.xpfile';

    case PGP = '.xgfile';

    public function label(): string
    {
        return match ($this) {
            self::SYSTEM => 'System key',
            self::USER => 'User key',
            self::SYSTEMUSER => 'System & User key',
            self::USERSUPLIED => 'User Supplied key',
            self::PASSPHRASE => 'Passphrase key',
            self::PGP => 'PGP key',
        };
    }

    public static function getAll()
    {
        return [
            self::SYSTEM->value,
            self::USER->value,
            self::SYSTEMUSER->value,
            self::USERSUPLIED->value,
            self::PASSPHRASE->value,
            self::PGP->value,
        ];
    }
}
