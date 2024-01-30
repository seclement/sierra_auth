<?php

namespace Drupal\sierra_auth;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Http\ClientFactory;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Service for Sierra Authentication API calls.
 */
class SierraAuthService {

  protected $clientFactory;
  protected $configFactory;
  protected $session;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Http\ClientFactory $client_factory
   *   The HTTP client factory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
   *   The session service.
   */
  public function __construct(ClientFactory $client_factory, ConfigFactoryInterface $config_factory, SessionInterface $session) {
    $this->clientFactory = $client_factory;
    $this->configFactory = $config_factory;
    $this->session = $session;
  }

  /**
   * Authenticate and get an access token.
   *
   * @return string|false
   *   The access token or false on failure.
   */
  public function authenticate() {
    $config = $this->configFactory->get('sierra_auth.settings');

    $url = $config->get('api_url') . '/v6/token';
    $method = 'POST';
    $headers = [
      'Authorization' => 'Basic ' . $config->get('api_credentials'),
    ];

    try {
      $response = $this->clientFactory->fromOptions()->request($method, $url, [
        'headers' => $headers,
      ]);

    // Log the response status code.
    \Drupal::logger('sierra_auth')->debug('Received response with status code: @code', ['@code' => $response->getStatusCode()]);

    if ($response->getStatusCode() == 200) {
      $data = json_decode($response->getBody(), TRUE);
      $token = $data['access_token'];

      // Store the token in the session for future use.
      $this->session->set('sierra_access_token', $token);

      return $token;
    } else {
      // Log the failed authentication.
      \Drupal::logger('sierra_auth')->error('Authentication failed. HTTP error code: @code', ['@code' => $response->getStatusCode()]);
    }
    } catch (RequestException $e) {
    // Log the exception.
    \Drupal::logger('sierra_auth')->error('Error during authentication request: @message', ['@message' => $e->getMessage()]);
    }

    return FALSE;
  }
}
