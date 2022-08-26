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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class Loader implements ExtensionInterface
{
    public const SERVICE_ID = 'task.gitlab_lint';

    public function load(ContainerBuilder $container): void
    {
        $container->register(self::SERVICE_ID, GitlabLintTask::class)
            ->addArgument(new Reference('process_builder'))
            ->addArgument(new Reference('formatter.raw_process'))
            ->addTag('grumphp.task', [
                'task' => 'gitlab_lint',
            ]);
    }
}
