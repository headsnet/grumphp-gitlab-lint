<?php
/*
 * This file is part of the Headsnet GrumPHPGitlabLint package.
 *
 * (c) Headstrong Internet Services Ltd 2022
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Headsnet\GrumPHP\GitlabLint;

use GrumPHP\Extension\ExtensionInterface;

final class Loader implements ExtensionInterface
{
    public function imports(): iterable
    {
        yield __DIR__ . '/Resources/config/services.yaml';
    }
}
