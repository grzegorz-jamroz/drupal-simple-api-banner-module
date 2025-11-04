<?php

declare(strict_types=1);

namespace Drupal\simple_api_banner\Hook;

use Drupal\Core\Hook\Attribute\Hook;

class SimpleApiBannerThemeHooks
{
  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme() : array
  {
    return [
      'simple_api_banner' => [
        'variables' => [
          'text' => NULL,
          'author' => NULL,
        ],
        'template' => 'simple-api-banner',
      ],
    ];
  }
}
