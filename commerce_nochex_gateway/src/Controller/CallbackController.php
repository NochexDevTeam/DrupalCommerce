<?php

namespace Drupal\commerce_nochex_gateway\Controller;

use Drupal\commerce_order\Entity\Order;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Endpoints for the routes defined.
 */
class CallbackController extends ControllerBase {
 
  /**
   * Callback action.
   *
   * Listen for callbacks from QuickPay and creates any payment specified.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function callback() {
		echo "callback";
   /* $content = json_decode($request->getContent());

    $order = Order::load($content->variables->order);

    $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
    $payment = $payment_storage->create([
      'state' => $content->accepted ? 'Accepted' : 'Failed',
      'amount' => $order->getTotalPrice(),
      'payment_gateway' => $content->variables->payment_gateway,
      'order_id' => $order->id(),
      'remote_id' => $content->id,
      'remote_state' =>  $this->getRemoteState($content),
    ]);

    $payment->save();

    return new Response();*/
  }

}
