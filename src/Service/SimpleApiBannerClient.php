<?php

declare(strict_types=1);

namespace Drupal\simple_api_banner\Service;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Cache\CacheBackendInterface;
use Exception;
use GuzzleHttp\ClientInterface;
use PlainDataTransformer\Transform;

class SimpleApiBannerClient
{
  public function __construct(
    private ClientInterface $httpClient,
    private SimpleApiBannerConfig $config,
    private CacheBackendInterface $cacheBackend,
  )
  {
  }

  /**
   * @return array<string, string>
   */
  public function fetchData(): array
  {
    $endpointUrl = $this->config->getEndpointUrl();
    $responseJsonTextPath = $this->config->getResponseJsonTextPath();
    $responseJsonAuthorPath = $this->config->getResponseJsonAuthorPath();
    $cid = 'simple_api_banner_data';
    $cache = $this->cacheBackend->get($cid);

    if ($cache !== false) {
      return $cache->data;
    }

    if ($endpointUrl === '') {
      throw new \InvalidArgumentException('API endpoint not configured.');
    }

    $response = $this->httpClient->request(
      'GET',
      $endpointUrl,
      [
        'timeout' => 5,
      ]
    );

    if ($response->getStatusCode() !== 200) {
      throw new Exception('API returned non-200 status.');
    }

    $data = json_decode($response->getBody()->getContents(), true);
    $text = NestedArray::getValue($data, explode('.', $responseJsonTextPath));
    $author = NestedArray::getValue($data, explode('.', $responseJsonAuthorPath));
    $data = [
      '#text' => Transform::toString($text),
      '#author' => Transform::toString($author),
    ];

    $cacheDuration = $this->config->getCacheDuration();

    if ($cacheDuration > 0) {
      $this->cacheBackend->set($cid, $data, time() + $cacheDuration, ['simple_api_banner']);
    }

    return $data;
  }
}
