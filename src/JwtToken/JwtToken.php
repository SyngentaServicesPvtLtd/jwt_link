<?php

namespace Drupal\jwt_link\JwtToken;

use Firebase\JWT\JWT;

/**
 * Class JwtToken.
 *
 * @package Drupal\jwt_link\JwtToken
 */
class JwtToken {

  /**
   * {@inheritdoc}
   */
  public function jwtEncodedToken() {
    $php_jwt = new JWT();
    $jwt_config = \Drupal::config('jwt_link.config');

    // Token expiration.
    $exp = time() + (60 * $jwt_config->get('jwt_link_expiration'));

    // Default tokens.
    $token_exp = array(
      "exp" => $exp,
    );

    $token_values = $jwt_config->get('jwt_link_payload_data');
    if (!empty($token_values)) {
      $token = \Drupal::token();
      $configured_token = array();
      $token_values = array_filter(explode("\n", $token_values));
      foreach ($token_values as $item) {
        list($token_key, $value) = explode('|', $item);
        $token_item = $token->replace($value);
        $configured_token[trim($token_key)] = trim($token_item);
      }
      $payload = array_merge($configured_token, $token_exp);
    }

    $jwt_encoded_token = $php_jwt->encode($payload, $jwt_config->get('jwt_link_key'), $jwt_config->get('jwt_link_algorithm'));

    return $jwt_encoded_token;

  }

  /**
   * {@inheritdoc}
   */
  public function jwtUrl() {
    $jwt_config = \Drupal::config('jwt_link.config');
    $jwt_encoded_token = $this->jwtEncodedToken();
    $link_url = $jwt_config->get('jwt_link_auth_server');
    $link_arg = $jwt_config->get('jwt_link_arg');
    $jwt_encoded_url = "{$link_url}?{$link_arg}={$jwt_encoded_token}";
    return $jwt_encoded_url;
  }

}
