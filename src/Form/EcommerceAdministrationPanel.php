<?php

/**
 * @file
 * Contains \Drupal\api_ecommerce\Form\EcommerceAdministrationPanel.
 */

namespace Drupal\api_ecommerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class EcommerceAdministrationPanel extends FormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'ecommerce_admin_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $node["host"] = "https://credomatic.compassmerchantsolutions.com/api/transact.php";
    $node["key"] = "wrt435t34tgergser5t43";
    
    //$form['#action'] = 'http://example.com';
    $form['host'] = [
    '#type' => 'textfield',
    '#title' => $this->t('Host'),
    '#default_value' => $node["host"],
    '#size' => 60,
    '#maxlength' => 128,
    '#required' => TRUE
    ];
    $form['key'] = [
    '#type' => 'textfield',
    '#title' => $this->t('Key'),
    '#default_value' => $node["key"],
    '#size' => 60,
    '#maxlength' => 128,
    '#required' => TRUE
    ];
    $form['show'] = [
    '#type' => 'submit',
    '#value' => $this->t('Aceptar'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) { 
    /*if (strpos($form_state->getValue('email'), '.com') === FALSE) {
      $form_state->setErrorByName('email', $this->t('This is not a .com email address.'));
    }*/
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message($this->t('Su configuración ha sido guardada.'));
    //drupal_set_message($this->t('Your email address is @email', ['@email' => $form_state->getValue('email')]));
  }

}
