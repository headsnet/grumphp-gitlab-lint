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

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class GitlabApiClient
{
    private const API_URL = 'https://gitlab.com/api/v4/ci/lint';

    private string $apiToken;

    private string $configFile;

    public function __construct(string $apiToken, string $configFile)
    {
        $this->configFile = $configFile;
        $this->apiToken = $apiToken;
    }

    /**
     * @return array<string, string>
     *
     * @throws DecodingExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function lint(): array
    {
        $httpClient = HttpClient::create();

        $yamlEncoder = new YamlEncoder();
        $jsonEncoder = new JsonEncoder();

        if (! file_exists($this->configFile)) {
            throw GitlabLinterException::fileNotFound($this->configFile);
        }

        $fileContents = (string) file_get_contents($this->configFile);

        $jsonData = $jsonEncoder->encode(
            $yamlEncoder->decode($fileContents, YamlEncoder::FORMAT),
            JsonEncoder::FORMAT
        );

        $jsonData = str_replace('\\', '\\\\', $jsonData);
        $jsonData = str_replace('"', '\"', $jsonData);

        $response = $httpClient->request('POST', self::API_URL, [
            'headers' => [
                'Content-Type' => 'application/json',
                'PRIVATE-TOKEN' => $this->apiToken,
            ],
            'body' => sprintf('{"content": "%s"}', $jsonData),
        ]);

        if ($response->getStatusCode() === 401) {
            throw GitlabLinterException::unauthorized();
        }

        if ($response->getStatusCode() === 500) {
            throw GitlabLinterException::error500();
        }

        return $response->toArray();
    }
}
