<?php

namespace Drupal\jwt_link\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\jwt_link\JwtToken\JwtToken;


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
  	$jwt_token = new JwtToken();
  	$jwt_config = \Drupal::config('jwt_link.config');
    $jwt_encoded_url = $jwt_token->jwtUrl();
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