<?php

namespace Drupal\commerce_nochex_gateway\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\Entity\PaymentInterface;
use Drupal\commerce_payment\PaymentMethodTypeManager;
use Drupal\commerce_payment\PaymentTypeManager;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayBase;
use Drupal\commerce_payplug\Services\PayPlugServiceInterface;
use Drupal\commerce_price\Calculator;
use Drupal\commerce_price\Price;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides the Nochex offsite Checkout payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "nochex_redirect_checkout",
 *   label = @Translation("Nochex (Redirect to Nochex)"),
 *   display_label = @Translation("Nochex"),
 *    forms = {
 *     "offsite-payment" = "Drupal\commerce_nochex_gateway\PluginForm\RedirectCheckoutForm",
 *   },
 * )
 */
class RedirectCheckout extends OffsitePaymentGatewayBase
{
  
  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
        'merchant_id' => '',
        'xmlCollect' => '',
      ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['merchant_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Merchant ID'),
      '#description' => $this->t('This is the Merchant ID / email address.'),
      '#default_value' => $this->configuration['merchant_id'],
      '#required' => TRUE,
    ];
	
	$form['xmlCollect'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Detailed Item Collection'),
      '#description' => $this->t('Display products in a table structured format on your Nochex Payment Page.'),
      '#default_value' => $this->configuration['xmlCollect'],
      '#required' => FALSE,
    ];
	
    return $form;
  }

  function onNotify(Request $request) {
    parent::onNotify($request);
	  	
	if(!empty($_POST)){
	// Get the POST information from Nochex server
	$postvars = http_build_query($_POST);
	ini_set("SMTP","mail.nochex.com" ); 
	$header = "From: apc@nochex.com";

	// Set parameters for the email
	$to = 'james.lugton@nochex.com';
	$url = "https://www.nochex.com/apcnet/apc.aspx";

	// Curl code to post variables back
	$ch = curl_init(); // Initialise the curl tranfer
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars); // Set POST fields
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60); // set connection time out variable - 60 seconds	
	$output = curl_exec($ch); // Post back
	curl_close($ch);

	// Put the variables in a printable format for the email
	$debug = "IP -> " . $_SERVER['REMOTE_ADDR'] ."\r\n\r\nPOST DATA:\r\n"; 
	foreach($_POST as $Index => $Value) 
	$debug .= "$Index -> $Value\r\n"; 
	$debug .= "\r\nRESPONSE:\r\n$output";
	 
	//If statement
	if (!strstr($output, "AUTHORISED")) {  // searches response to see if AUTHORISED is present if it isn’t a failure message is displayed
		$msg = "APC was not AUTHORISED.\r\n\r\n$debug";  // displays debug message
	} 
	else { 
		$msg = "APC was AUTHORISED.\r\n\r\n$debug"; // if AUTHORISED was found in the response then it was successful
		// whatever else you want to do 
	}
   	
	  $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
      $payment = $payment_storage->create([
        'state' => $output,
        'amount' => new Price($_POST["amount"],"GBP"),
        'payment_gateway' => 'nochex',
        'order_id' => $_POST["order_id"],
        'remote_state' => $output,
        'remote_id' => $_POST["order_id"],
      ]);
	  
      $payment->save();
	  
	  drupal_set_message('Payment was processed');

} 
	}

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['merchant_id'] = $values['merchant_id'];
      $this->configuration['xmlCollect'] = $values['xmlCollect'];
    }
  }

 
  /**
   * Returns an array of languages supported by Quickpay.
   *
   * @return array
   *   Array with key being language codes, and value being names.
   */
  protected function getLanguages()
  {
    return [
      'da' => $this->t('Danish'),
      'de' => $this->t('German'),
      'en' => $this->t('English'),
      'fo' => $this->t('Faeroese'),
      'fr' => $this->t('French'),
      'gl' => $this->t('Greenlandish'),
      'it' => $this->t('Italian'),
      'no' => $this->t('Norwegian'),
      'nl' => $this->t('Dutch'),
      'pl' => $this->t('Polish'),
      'se' => $this->t('Swedish'),
    ];
  }
}
