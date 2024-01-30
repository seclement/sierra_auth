<?php

namespace Drupal\sierra_auth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\sierra_auth\SierraAuthService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for Sierra Set Aside.
 */
class SierraAuthController extends ControllerBase {

  protected $sierraAuthService;
  protected $configFactory;

  /**
   * Constructor.
   *
   * @param \Drupal\sierra_auth\SierraAuthService $sierra_auth_service
   *   The Sierra Set Aside service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(SierraAuthService $sierra_auth_service, ConfigFactoryInterface $config_factory) {
    $this->sierraAuthService = $sierra_auth_service;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('sierra_auth.service'),
      $container->get('config.factory')
    );
  }

  /**
   * Example action to authenticate.
   */
  public function authenticateAction() {
    $options = $this->configFactory->get('sierra_auth.settings')->get('options');
    $access_token = $this->sierraAuthService->authenticate($options);

    if ($access_token) {
      // Authentication successful, do something with $access_token.
      return [
        '#markup' => $this->t('Authentication successful! Access Token: @token', ['@token' => $access_token]),
      ];
    } else {
      // Authentication failed, handle accordingly.
      return [
        '#markup' => $this->t('Authentication failed.'),
      ];
    }
  }

}