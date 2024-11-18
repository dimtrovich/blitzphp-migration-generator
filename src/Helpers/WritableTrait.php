<?php

/**
 * This file is part of dimtrovich/blitzphp-migration-generator".
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\MigrationGenerator\Helpers;

trait WritableTrait
{
    public bool $writable = true;

    public function markAsWritable(bool $writable = true)
    {
        $this->writable = $writable;

        return $this;
    }

    public function isWritable()
    {
        return $this->writable;
    }
}
