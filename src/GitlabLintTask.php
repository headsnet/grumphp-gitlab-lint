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

use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GitlabLintTask extends AbstractExternalTask
{
    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'api_token' => '',
            'gitlab_file' => dirname(__DIR__, 4) . '/.gitlab-ci.yml',
            'gitlab_url' => 'gitlab.com',
        ]);

        $resolver
            ->addAllowedTypes('api_token', ['string'])
            ->addAllowedTypes('gitlab_file', ['string'])
            ->addAllowedTypes('gitlab_url', ['string'])
        ;

        return $resolver;
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof GitPreCommitContext || $context instanceof RunContext;
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfig()->getOptions();

        if (! file_exists($config['gitlab_file'])) {
            throw GitlabLinterException::fileNotFound($config['gitlab_file']);
        }

        $files = $context->getFiles()->name(basename($config['gitlab_file']));
        if (count($files) === 0) {
            return TaskResult::createSkipped($this, $context);
        }

        $apiClient = new GitlabApiClient($config);

        /** @var array{valid: bool, errors: array<string>} $response */
        $response = $apiClient->lint();

        if ($response['valid'] !== true) {
            return TaskResult::createFailed($this, $context, implode('; ', $response['errors']));
        }

        return TaskResult::createPassed($this, $context);
    }
}
