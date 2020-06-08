<?php

namespace Drupal\jwt_link\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Firebase\JWT\JWT;


/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "jwt_link_block",
 *   admin_label = @Translation("JWT link"),
 * )
 */
class JwtBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
  	$jwt_token = new JWT();
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


  	$jwt_encoded_token = $jwt_token->encode($payload, $jwt_config->get('jwt_link_key'), $jwt_config->get('jwt_link_algorithm'));

    $link_url = $jwt_config->get('jwt_link_auth_server');
    $link_arg = $jwt_config->get('jwt_link_arg');
    $jwt_encoded_url = "{$link_url}?{$link_arg}={$jwt_encoded_token}";

    return [
      '#markup' => '<a href=' . $jwt_encoded_url . ' target = "_blank">' . $jwt_config->get('jwt_link_title') . '</a>',
    ];
  }

  /**
	* {@inheritdoc}
	*/
  public function getCacheMaxAge() {
    return 0;
  }

   /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    if ($account->isAuthenticated()) {
      return AccessResult::allowed();
    }
    return AccessResult::forbidden();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['jwt_link_block_settings'] = $form_state->getValue('jwt_link_block_settings');
  }	
}