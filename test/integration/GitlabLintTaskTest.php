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

use GrumPHP\Collection\FilesCollection;
use GrumPHP\Formatter\RawProcessFormatter;
use GrumPHP\Process\ProcessBuilder;
use GrumPHP\Task\Config\Metadata;
use GrumPHP\Task\Config\TaskConfig;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\TaskInterface;
use Headsnet\GrumPHP\GitlabLint\GitlabLinterException;
use Headsnet\GrumPHP\GitlabLint\GitlabLintTask;
use PHPUnit\Framework\TestCase;

class GitlabLintTaskTest extends TestCase
{
    private const VALID_GITLAB_CI_YML = '../fixtures/valid-gitlab-ci.yml';

    private const INVALID_GITLAB_CI_YML = '../fixtures/invalid-gitlab-ci.yml';

    const GITLAB_TEST_TOKEN = 'glpat-M8-Lb3zhAxa7RG6oVBox'; // Token from a test Gitlab account!

    public function test_handles_missing_file(): void
    {
        $missingFile = 'this-file-does-not-exist.yml';

        [
            $context,
            $sut
        ] = $this->buildTask($missingFile, self::GITLAB_TEST_TOKEN);

        $this->expectExceptionObject(GitlabLinterException::fileNotFound(
            sprintf('%s/%s', __DIR__, $missingFile)
        ));

        $sut->run($context);
    }

    public function test_handles_authentication_error(): void
    {
        [
            $context,
            $sut
        ] = $this->buildTask(self::VALID_GITLAB_CI_YML, '');

        $this->expectExceptionObject(GitlabLinterException::unauthorized());

        $sut->run($context);
    }

    /**
     * @dataProvider gitlabFileDataProvider
     */
    public function test_can_lint_valid_and_invalid_files(string $file, bool $expectedResult): void
    {
        [
            $context,
            $sut
        ] = $this->buildTask($file, self::GITLAB_TEST_TOKEN);

        $result = $sut->run($context);

        $this->assertEquals($expectedResult, $result->isPassed(), $result->getMessage());
    }

    /**
     * @return array<array<bool|string>>
     */
    public function gitlabFileDataProvider(): array
    {
        return [
            [
                self::VALID_GITLAB_CI_YML,
                true,
            ],
            [
                self::INVALID_GITLAB_CI_YML,
                false,
            ],
        ];
    }

    /**
     * @return array{0: ContextInterface, 1: TaskInterface}
     */
    private function buildTask(string $file, string $apiToken): array
    {
        $processBuilder = $this->createMock(ProcessBuilder::class);
        $rawProcessFormatter = $this->createMock(RawProcessFormatter::class);
        $context = new class() implements ContextInterface {
            public function getFiles(): FilesCollection
            {
                return new FilesCollection();
            }
        };

        $sut = new GitlabLintTask(
            $processBuilder,
            $rawProcessFormatter
        );

        $sut = $sut->withConfig(
            new TaskConfig(
                'gitlab_lint',
                [
                    'api_token' => $apiToken,
                    'gitlab_file' => sprintf('%s/%s', __DIR__, $file),
                ],
                new Metadata([])
            )
        );

        return [
            $context,
            $sut,
        ];
    }
}
