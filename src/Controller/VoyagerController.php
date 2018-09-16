<?php

namespace Drupal\graphql\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\graphql\Entity\ServerInterface;
use Drupal\graphql\GraphQL\Utility\Introspection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for the GraphQL Voyager visualisation API.
 */
class VoyagerController implements ContainerInjectionInterface {
  /**
   * The introspection service.
   *
   * @var \Drupal\graphql\GraphQL\Utility\Introspection
   */
  protected $introspection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('graphql.introspection'));
  }

  /**
   * VoyagerController constructor.
   *
   * @param \Drupal\graphql\GraphQL\Utility\Introspection $introspection
   *   The GraphQL introspection service.
   */
  public function __construct(Introspection $introspection) {
    $this->introspection = $introspection;
  }

  /**
   * Display for the GraphQL Voyager visualization API.
   *
   * @param \Drupal\graphql\Entity\ServerInterface $graphql_server
   *   The server.
   *
   * @return array The render array.
   *   The render array.
   */
  public function viewVoyager(ServerInterface $graphql_server) {
    $introspectionData = $this->introspection->introspect($graphql_server->id());

    return [
      '#type' => 'markup',
      '#markup' => '<div id="graphql-voyager"></div>',
      '#attached' => [
        'library' => ['graphql/voyager'],
        'drupalSettings' => [
          'graphqlIntrospectionData' => $introspectionData,
        ],
      ],
    ];
  }

}
