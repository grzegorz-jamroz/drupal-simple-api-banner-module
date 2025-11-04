<?php

declare(strict_types=1);

namespace Drupal\simple_api_banner\Service;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\ImmutableConfig;
use PlainDataTransformer\Transform;

class SimpleApiBannerConfig
{
  private ImmutableConfig $config;

  public function __construct(
    ConfigFactory $configFactory,
  )
  {
    $this->config = $configFactory->get('simple_api_banner.settings');
  }

  public function getEndpointUrl(): string
  {
    return Transform::toString($this->config->get('endpoint_url') ?? '');
  }

  public function getResponseJsonTextPath(): ?string
  {
    return Transform::toString($this->config->get('response_json_text_path') ?? '');
  }

  public function getResponseJsonAuthorPath(): ?string
  {
    return Transform::toString($this->config->get('response_json_author_path') ?? '');
  }

  public function getCacheDuration(): int
  {
    $maxAge = 3600;
    $cacheDuration = $this->config->get('cache_duration') ?? $maxAge;

    if (is_numeric($cacheDuration)) {
      $cacheDuration = Transform::toInt($cacheDuration);

      return $cacheDuration >= 0 ? $cacheDuration : $maxAge;
    }

    if (is_string($cacheDuration) === false) {
      return $maxAge;
    }

    try {
      $cacheDuration = Transform::toDateTimeImmutable($cacheDuration);
      $diff = $cacheDuration->getTimestamp() - new \DateTimeImmutable()->getTimestamp();

      if ($diff > 0) {
        $maxAge = $diff;
      }
    } catch (\Throwable) {
    }

    return $maxAge;
  }
}
