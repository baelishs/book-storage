<?php

namespace App\Providers\Books;

use App\Exceptions\Books\ExternalBookServiceException;
use App\Mappers\External\GoogleBooksMapper;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class GoogleBooksProvider implements BookProviderInterface
{
    public const SOURCE = 'google';

    private const API_URL = 'https://www.googleapis.com/books/v1/volumes';
    private const QUERY_LIMIT = 20;

    public function __construct(
        private readonly ClientInterface $client,
        private readonly GoogleBooksMapper $mapper,
    ) {
    }

    /**
     * @throws ExternalBookServiceException
     */
    public function search(string $query): array
    {
        try {
            $response = $this->client->get(self::API_URL, [
                'query' => [
                    'q' => $query,
                    'maxResults' => self::QUERY_LIMIT,
                ],
                'http_errors' => true,
            ]);

            $rawBody = (string) $response->getBody();
            $data = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);

            if (!isset($data['items']) || !is_array($data['items'])) {
                return [];
            }

            return $this->mapper->hydrateToExternalDTO($data['items']);
        } catch (RequestException $e) {
            throw new ExternalBookServiceException(
                message: 'Google Books API request failed',
                code: 503,
                previous: $e,
            );
        } catch (GuzzleException $e) {
            throw new ExternalBookServiceException(
                message: 'Google Books service unavailable',
                code: 503,
                previous: $e,
            );
        } catch (\JsonException $e) {
            throw new ExternalBookServiceException(
                message: 'Failed to parse Google Books API response',
                code: 503,
                previous: $e,
            );
        }
    }
}
