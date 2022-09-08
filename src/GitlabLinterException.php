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

final class GitlabLinterException extends \RuntimeException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function missingToken(): self
    {
        return new self('Your Gitlab API token cannot be read!');
    }

    public static function unauthorized(): self
    {
        return new self('Your Gitlab API token has not been authorized by Gitlab!');
    }

    public static function error500(): self
    {
        return new self('Gitlab returned error 500!');
    }

    public static function fileNotFound(string $file): self
    {
        return new self(sprintf('Cannot find the Gitlab CI file "%s"', $file));
    }
}
