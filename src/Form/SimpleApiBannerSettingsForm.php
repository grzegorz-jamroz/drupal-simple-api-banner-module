<?php

declare(strict_types=1);

namespace Drupal\simple_api_banner\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SimpleApiBannerSettingsForm extends ConfigFormBase
{
  protected function getEditableConfigNames(): array
  {
    return ['simple_api_banner.settings'];
  }

  public function getFormId(): string
  {
    return 'simple_api_banner_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array
  {
    $config = $this->config('simple_api_banner.settings');

    $form['endpoint_url'] = [
      '#type' => 'url',
      '#title' => $this->t('API Endpoint URL'),
      '#description' => $this->t('The API endpoint URL (e.g., `https://api.quotable.io/random`)'),
      '#default_value' => $config->get('endpoint_url') ?? '',
    ];

    $form['response_json_text_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Response JSON Text Path'),
      '#description' => $this->t('The path to the desired text in the JSON response (e.g. content.text.value).'),
      '#default_value' => $config->get('response_json_text_path') ?? 'content',
    ];

    $form['response_json_author_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Response JSON Author Path'),
      '#description' => $this->t('The path to an author or link in the JSON response (e.g., `author`).'),
      '#default_value' => $config->get('response_json_author_path') ?? 'author',
    ];

    $form['cache_duration'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cache Duration'),
      '#description' => $this->t('The duration to cache the API response (e.g., "1 hour").'),
      '#default_value' => $config->get('cache_duration') ?? '1 hour',
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void
  {
    $this->config('simple_api_banner.settings')
      ->set('endpoint_url', $form_state->getValue('endpoint_url'))
      ->set('response_json_text_path', $form_state->getValue('response_json_text_path'))
      ->set('response_json_author_path', $form_state->getValue('response_json_author_path'))
      ->set('cache_duration', $form_state->getValue('cache_duration'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}
