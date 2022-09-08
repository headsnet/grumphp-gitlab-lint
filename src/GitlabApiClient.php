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
    private const API_URL = 'https://%s/api/v4/ci/lint';

    private string $gitlabToken;

    private string $gitlabFile;

    private string $gitlabInstance;

    /**
     * @param array<string> $config
     */
    public function __construct(array $config)
    {
        $this->gitlabToken = $config['api_token'];
        $this->gitlabFile = $config['gitlab_file'];
        $this->gitlabInstance = $config['gitlab_url'];
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
        if (strlen($this->gitlabToken) === 0) {
            throw GitlabLinterException::missingToken();
        }

        $httpClient = HttpClient::create();

        $url = sprintf(self::API_URL, $this->gitlabInstance);
        $response = $httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'PRIVATE-TOKEN' => $this->gitlabToken,
            ],
            'body' => sprintf('{"content": "%s"}', $this->yamlConfigEncodedAsJson()),
        ]);

        if ($response->getStatusCode() === 401) {
            throw GitlabLinterException::unauthorized();
        }

        if ($response->getStatusCode() === 500) {
            throw GitlabLinterException::error500();
        }

        return $response->toArray();
    }

    private function yamlConfigEncodedAsJson(): string
    {
        $fileContents = (string) file_get_contents($this->gitlabFile);

        $yamlEncoder = new YamlEncoder();
        $jsonEncoder = new JsonEncoder();

        $jsonData = $jsonEncoder->encode(
            $yamlEncoder->decode($fileContents, YamlEncoder::FORMAT),
            JsonEncoder::FORMAT
        );

        $jsonData = str_replace('\\', '\\\\', $jsonData);

        return str_replace('"', '\"', $jsonData);
    }
}
