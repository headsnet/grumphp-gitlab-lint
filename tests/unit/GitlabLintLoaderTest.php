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

namespace Headsnet\GrumPHP\GitlabLint\Test;

use Headsnet\GrumPHP\GitlabLint\Loader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GitlabLintLoaderTest extends TestCase
{
    public function test_extension_loader(): void
    {
        $sut = new Loader();
        $container = new ContainerBuilder();

        $sut->load($container);

        $this->assertTrue($container->hasDefinition(Loader::SERVICE_ID));
    }
}
