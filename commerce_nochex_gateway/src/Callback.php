<?php

namespace Drupal\commerce_nochex_gateway;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class IPNHandler implements IPNHandlerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Constructs a new PaymentGatewayBase object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger channel.
   * @param \GuzzleHttp\ClientInterface $client
   *   The client.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, LoggerInterface $logger, ClientInterface $client) {
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger;
    $this->httpClient = $client;
  }

  /**
   * {@inheritdoc}
   */
  public function process(Request $request) {
   
   
  }

}
