<?php

declare(strict_types=1);

namespace Drupal\simple_api_banner\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\simple_api_banner\Service\SimpleApiBannerClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Simple Api Banner block type.
 */
#[Block(
  id: "simple_api_banner_block",
  admin_label: new TranslatableMarkup("Simple Api Banner"),
  category: new TranslatableMarkup("Content"),
)]
class SimpleApiBannerBlock extends BlockBase implements ContainerFactoryPluginInterface
{
  private LoggerChannelInterface $logger;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private SimpleApiBannerClient $client,
    LoggerChannelFactoryInterface $loggerFactory,
  )
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->logger = $loggerFactory->get('simple_api_banner');
  }

  public function build(): array
  {
    $block = [
      '#theme' => 'simple_api_banner',
      '#title' => '',
      '#text' => '',
      '#author' => '',
    ];

    try {
      return [
        ...$block,
        ...$this->client->fetchData(),
      ];
    } catch (\Throwable $e) {
      $this->logger->error('SimpleApiBannerBlock failed to fetch banner data', ['exception' => $e->getMessage()]);
    }

    return $block;
  }

  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('simple_api_banner.client'),
      $container->get('logger.factory'),
    );
  }
}
