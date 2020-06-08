<?php

namespace Drupal\jwt_link\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConfigForm.
 *
 * @package Drupal\jwt_link\Form
 */
class ConfigForm extends ConfigFormBase {   

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'jwt_link.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'jwt_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('jwt_link.config'); 
    $form['jwt_link_payload_data'] = [  
      '#type' => 'textarea',  
      '#title' => $this->t('Payload Data Parameters'),  
      '#description' => $this->t('The possible values this field can contain. Enter one value per line, in the format key|value. This field supports tokens. Example: email|[current-user:mail]'),  
      '#default_value' => $config->get('jwt_link_payload_data'),  
    ]; 

    $form['jwt_link_token_help'] = array(
      '#theme' => 'token_tree_link',
      '#token_types' => [],
    );

    $form['jwt_link_auth_server'] = [  
      '#type' => 'textfield',  
      '#title' => $this->t('Audience / Authorization Server'),  
      '#description' => $this->t('Site URL authorizing the generated token. Remote server Example: http://siteauthorizingthis.com/some/path'),
      '#size' => 100,  
      '#default_value' => $config->get('jwt_link_auth_server'),
    ]; 
    
    $form['jwt_link_arg'] = [  
      '#type' => 'textfield',  
      '#title' => $this->t('Link Token Argument'),  
      '#description' => $this->t('Token variable name for URL. Typically "jwt" or "token". Example: http://siteauthorizingthis.com?jwt={Encoded Token}'),
      '#size' => 100,  
      '#default_value' => $config->get('jwt_link_arg'),
    ]; 

    $form['jwt_link_title'] = [  
      '#type' => 'textfield',  
      '#title' => $this->t('Link Title'),  
      '#description' => $this->t('A title for the link rather that outputting the long URL. Example: Visit site..'),
      '#size' => 100,  
      '#default_value' => $config->get('jwt_link_title'),
    ]; 

    $form['jwt_link_key'] = [  
      '#type' => 'textfield',  
      '#title' => $this->t('Key'),  
      '#description' => $this->t('Some key or random string for encrypting the data. Recommended: 512-bit key'),
      '#size' => 100,  
      '#default_value' => $config->get('jwt_link_key'),
    ]; 

    $form['jwt_link_algorithm'] = [  
      '#type' => 'select',  
      '#title' => $this->t('Encryption Algorithm'),
      '#options' => array(
        'HS256' => 'HS256',
        'HS512' => 'HS512',
        'HS384' => 'HS384',
      ), 
      '#description' => $this->t('Algorithm for encryption.'), 
      '#default_value' => $config->get('jwt_link_algorithm'),
    ]; 

    $form['jwt_link_expiration'] = [  
      '#type' => 'textfield',  
      '#title' => $this->t('Token expiration'),  
      '#description' => $this->t('The amount of time until the token expires.'),
      '#size' => 1,
      '#maxlength' => 2,
      '#field_suffix' => 'minutes',  
      '#default_value' => $config->get('jwt_link_expiration'),
    ]; 

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $values = $form_state->getValues();

    if (isset($values['jwt_link_payload_data'])) {
      $this->config('jwt_link.config')->set('jwt_link_payload_data', $values['jwt_link_payload_data'])->save();
    }

    if (isset($values['jwt_link_auth_server'])) {
      $this->config('jwt_link.config')->set('jwt_link_auth_server', $values['jwt_link_auth_server'])->save();
    }

    if (isset($values['jwt_link_arg'])) {
      $this->config('jwt_link.config')->set('jwt_link_arg', $values['jwt_link_arg'])->save();
    }

    if (isset($values['jwt_link_title'])) {
      $this->config('jwt_link.config')->set('jwt_link_title', $values['jwt_link_title'])->save();
    }

    if (isset($values['jwt_link_key'])) {
      $this->config('jwt_link.config')->set('jwt_link_key', $values['jwt_link_key'])->save();
    }

    if (isset($values['jwt_link_algorithm'])) {
      $this->config('jwt_link.config')->set('jwt_link_algorithm', $values['jwt_link_algorithm'])->save();
    }

    if (isset($values['jwt_link_expiration'])) {
      $this->config('jwt_link.config')->set('jwt_link_expiration', $values['jwt_link_expiration'])->save();
    }
  }

}
