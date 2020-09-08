<?php
/**
 * @file
 * Contains \Drupal\shortend_links\Form\ShortendLinkForm.
 */
namespace Drupal\shortend_links\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ShortendLinkForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'shortened_link_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['long_link'] = array(
      '#type' => 'textfield',
      '#title' => t('Long Link'),
      '#placeholder' => $this->t('Shorten your link')
    );
    
    $form['shorten_link'] = array(
      '#type' => 'hidden',
      '#title' => t('Shorten Link'),
      '#placeholder' => $this->t('Shorten your link')
    );
   
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Shorten Link'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  	/**
	 * (@inheritdoc)
	 * Validate the form
	 */

	public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->getValue('long_link') || empty($form_state->getValue('long_link'))) {
      $form_state->setErrorByName('long_link', $this->t('Your textbox is empty'));
    }
	}


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

    $field = $form_state->getValues();
    $long_link_value = $field['long_link'];
    $shorten_link_value = $field['shorten_link'];

    $shorten_link_value = substr(md5($long_link_value.mt_rand()),0,8);

    $final_shorten = 'http://localhost/fortesting/web/c/'. $shorten_link_value;

    $field  = array(
      'long_link'   =>  $long_link_value,
      'short_link' =>  $final_shorten,
			'uid'=> $user->id(),
      );

   $query = \Drupal::database();
   $id = $query ->insert('shortend_links')
       ->fields($field)
       ->execute();

   drupal_set_message("succesfully saved");

   $response = new RedirectResponse("http://localhost/fortesting/web/shortened-link/" . $id);
   $response->send();
    
   }
}