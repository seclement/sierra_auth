<?php

namespace Drupal\sierra_auth\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for configuring Sierra Set Aside module settings.
 */
class SierraAuthSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['sierra_auth.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sierra_auth_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('sierra_auth.settings');

    $form['api_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sierra API URL'),
      '#default_value' => $config->get('api_url'),
      '#description' => $this->t('Enter the Sierra API URL.'),
    ];

    $form['api_credentials'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Sierra API Credentials'),
        '#default_value' => $config->get('api_credentials'),
        '#description' => $this->t('Enter the Sierra API credentials.'),
      ];

    $form['proxy_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Proxy'),
      '#default_value' => $config->get('proxy_enabled', FALSE),
      '#description' => $this->t('Check this box to enable the proxy.'),
    ];
  
    $form['proxy_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Proxy URL'),
      '#default_value' => $config->get('proxy_url', ''),
      '#description' => $this->t('Enter the Proxy URL if the proxy is enabled.'),
      '#states' => [
        'visible' => [
          ':input[name="proxy_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('sierra_auth.settings')
      ->set('api_url', $form_state->getValue('api_url'))
      ->set('api_credentials', $form_state->getValue('api_credentials'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
