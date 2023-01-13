<?php

namespace Ringli;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

/**
 * Minimal wrapper around Guzzle http client
 *
 * Responsible for bootstrapping the http client
 */
class Client
{
    /**
     * HttpClient client.
     */
    protected HttpClient $httpClient;

    /**
     * The Circle CI Token.
     */
    protected string $token;

    /**
     * The Circle CI Org.
     */
    protected string $org;

    /**
     * The base URL for the API.
     */
    protected string $baseURL = "https://circleci.com/api/v2";

    /**
     * Constructor
     */
    public function __construct(HttpClient $httpClient, string $token, string $org)
    {
        $this->httpClient = $httpClient;
        $this->token = $token;
        $this->org = $org;
    }

    /**
     * Responsible for augmenting the request to the endpoint.
     *
     * @return ResponseInterface
     */
    protected function request(string $verb, string $path): ResponseInterface
    {
        $url = $this->baseURL . $path . "?org-slug=" . $this->org;

        $response = $this->httpClient->request($verb, $url, [
            RequestOptions::HEADERS => ["Circle-Token" => $this->token]
        ]);

        return $response;
    }

    /**
     * Getter for the CircleCI Org.
     *
     * @return string
     */
    public function getOrg(): string
    {
        return $this->org;
    }

    /**
     * Getter for the pipelines
     *
     * @return array<mixed>
     */
    public function getPipelines(): array
    {
        $response = $this->request('GET', '/pipeline');
        return json_decode((string) $response->getBody(), true);
    }

    /**
     * Getting for workflows for a given pipeline
     *
     * @param string $id The ID of the pipeline.
     *
     * @return array<mixed>
     */
    public function getWorkflowForPipeline(string $id): array
    {
        $response = $this->request('GET', "/pipeline/{$id}/workflow");
        return json_decode((string) $response->getBody(), true);
    }
}
