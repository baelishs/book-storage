<?php

namespace App\Providers\Books;

use App\Exceptions\Books\ExternalBookServiceException;
use App\Mappers\External\ManIvanovFerberMapper;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class MannIvanovFerberProvider implements BookProviderInterface
{
    private const API_URL = 'https://www.mann-ivanov-ferber.ru/book/search.ajax';
    public const SOURCE = 'MannIvanovFerber';

    public function __construct(
        private readonly ClientInterface $client,
        private readonly ManIvanovFerberMapper $mapper,
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
                    'limit' => 20,
                ],
                'http_errors' => true,
            ]);

            $body = (string) $response->getBody();
            $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($data)) {
                return [];
            }

            $items = $data['books'] ?? $data['items'] ?? $data['results'] ?? $data;

            if (!is_array($items)) {
                return [];
            }

            return $this->mapper->hydrateToExternalDTO($items);
        } catch (RequestException $e) {
            throw new ExternalBookServiceException(
                message: 'Man Ivanov Ferber Books API request failed',
                code: 503,
                previous: $e,
            );
        } catch (GuzzleException $e) {
            throw new ExternalBookServiceException(
                message: 'Man Ivanov Ferber Books service unavailable',
                code: 503,
                previous: $e,
            );
        } catch (\JsonException $e) {
            throw new ExternalBookServiceException(
                message: 'Failed to parse Man Ivanov Ferber Books API response',
                code: 503,
                previous: $e,
            );
        }
    }
}
