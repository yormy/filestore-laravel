<?php

namespace Yormy\FilestoreLaravel\Domain\Shared\Enums;

enum ServeType: string
{
    case URL = 'AS_URL';

    case DATA = 'AS_DATA';
}
