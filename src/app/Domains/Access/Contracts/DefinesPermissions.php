<?php

namespace App\Domains\Access\Contracts;

/**
 * Implemented by every module's {Thing}Permission enum (string-backed, `{resource}-{action}`).
 */
interface DefinesPermissions
{
    /** Group heading in the roles UI, e.g. "Catalog". */
    public function group(): string;

    public function description(): string;
}
