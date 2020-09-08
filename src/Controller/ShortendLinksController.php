<?php

namespace Drupal\shortend_links\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ShortendLinksController extends ControllerBase {

  public function content(Request $request) {
    $parameteres = \Drupal::routeMatch()->getParameters();
    $id = $parameteres->get('id');

    $result = \Drupal::database()->select('shortend_links', 'n')
    ->fields('n', array('id', 'long_link', 'short_link'))
    ->condition('id', $id)
    ->execute()->fetchAll();

    // Create the row element.
    $rows = array();
    foreach ($result as $row => $content) {
      $rows[] = array(
        'data' => array($content->id, $content->long_link, $content->short_link)); 
    }
    
    //------------------------------------
    $url7 = Url::fromUri($content->short_link);
     
    // We'll add some HTML attributes to the link. 
    $link_options = [
      'attributes' => [
        'target' => '_blank',
        'title' => 'Shorten Link Redirection',
      ],
    ];
    $url7->setOptions($link_options);
    $link7 = Link::fromTextAndUrl(t('Click here to Redirect'), $url7);
    $list[] = $link7;

    // Mount the render output.
    $output['links_example'] = [
      '#theme' => 'item_list',
      '#items' => $list,
      '#title' => $url7,
    ];
    //====================================

// Create the header.
/*     $header = array('id', 'long_link', 'short_link');
    $output = array(
      '#theme' => 'table',    // Here you can write #type also instead of #theme.
      '#header' => $header,
      '#rows' => $rows
    );
 */
    return $output;
  }

 public function customRedirect(Request $request) {

  $parameteres = \Drupal::routeMatch()->getParameters();
  $dst = "http://localhost/fortesting/web/c/" . $parameteres->get('dst');

  $result = \Drupal::database()->select('shortend_links', 'n')
    ->fields('n', array('id', 'long_link', 'short_link', 'counter'))
    ->condition('short_link', $dst)
    ->execute()->fetchAll();

    // Create the row element.
    $long_link ='';
    foreach ($result as $row => $content) {
      $long_link = $content->long_link;
      $count = $content->counter;
    }
    if(!empty($long_link)) {
      $response = new RedirectResponse($long_link);
      $count++;

      $field  = array(
        'counter'   =>  $count,
      );
      $query = \Drupal::database();
      $count = $query ->update('shortend_links')
       ->fields($field)
       ->execute();

      return $response->send();
    }

    else {
     return [
       '#markup' => $long_link,
     ];
    }
  
 } 
 
}
