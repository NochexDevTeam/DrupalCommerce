<?php

namespace Drupal\commerce_nochex_gateway\PluginForm;

use Drupal\commerce_payment\PluginForm\PaymentOffsiteForm;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RedirectCheckoutForm extends PaymentOffsiteForm {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $configuration = $this->getConfiguration();

    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;
	
	$payment_gateway_plugin = $payment->getPaymentGateway()->getPlugin();

    $data['version'] = 'v10';
    $data['merchant_id'] = $configuration['merchant_id']; 

	if($configuration['mode'] == "test"){
		$data['test_transaction'] = "100";
		$data['test_success_url'] = $form['#return_url'];
	}
	
	$data['order_id'] = $payment->getOrderId();	
	$data['cancel_url'] = $form['#cancel_url'];
	$data['success_url'] = $form['#return_url'];	
	$data['amount'] = $payment->getAmount()->getNumber();	
	$data['callback_url'] = $payment_gateway_plugin->getNotifyUrl()->toString();
	
	$order = $payment->getOrder();
 

 	$description = "";
	
	if ($configuration['xmlCollect'] == 1){
	$description = "Order - #" .$payment->getOrderId();
	$xmlCollect = "<items>";
	foreach ($order->getItems() as $order_item) {
		$xmlCollect .= "<item><id></id><name>" .$order_item->getTitle() ."</name><description>" .$order_item->getTitle() ."</description><quantity>". $order_item->getQuantity() . "</quantity><price>" . $order_item->getUnitPrice() . "</price></item>";
    }
	$xmlCollect .= "</items>";
	}else{
	
    foreach ($order->getItems() as $order_item) {
		$description .= "Item: " .$order_item->getTitle() ." - ". $order_item->getQuantity() . " x " . $order_item->getUnitPrice() . ", ";
    }
	
	}
	
	$profile = $order->getBillingProfile();
		/* Billing Details*/
		$data['billing_fullname'] = $profile->address->given_name . ", " . $profile->address->family_name;
		$data['billing_address'] = $profile->address->address_line1;
		$data['billing_city'] = $profile->address->locality;
		$data['billing_postcode'] = $profile->address->postal_code;
		$data['email_address'] = $order->getEmail();
		$data['description'] = $description;
		$data['xml_item_collection'] = $xmlCollect;
		
    return $this->buildRedirectForm(
      $form,
      $form_state,
      'https://secure.nochex.com/default.aspx',
      $data,
      PaymentOffsiteForm::REDIRECT_POST
    );
  }
 
  /**
   * @return array
   */
  private function getConfiguration() {
    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $this->entity;

    /** @var \Drupal\commerce_nochex_gateway\Plugin\Commerce\PaymentGateway\RedirectCheckout $payment_gateway_plugin */
    $payment_gateway_plugin = $payment->getPaymentGateway()->getPlugin();
    return $payment_gateway_plugin->getConfiguration();
  }
  
}
