<?php

namespace Drupal\graphql_core\Plugin\Deriver\InputTypes;

use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\graphql\Utility\StringHelper;
use Drupal\graphql_core\Plugin\Deriver\ViewDeriverBase;
use Drupal\views\Views;

/**
 * Derive input types for view contextual filters.
 */
class ViewContextualFilterInputDeriver extends ViewDeriverBase implements ContainerDeriverInterface {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($basePluginDefinition) {
    if ($this->entityTypeManager->hasDefinition('view')) {
      $viewStorage = $this->entityTypeManager->getStorage('view');

      foreach (Views::getApplicableViews('graphql_display') as list($viewId, $displayId)) {
        /** @var \Drupal\views\ViewEntityInterface $view */
        $view = $viewStorage->load($viewId);
        if (!$this->getRowResolveType($view, $displayId)) {
          continue;
        }

        /** @var \Drupal\graphql\Plugin\views\display\GraphQL $display */
        $display = $this->getViewDisplay($view, $displayId);
        $argumentsInfo = $this->getArgumentsInfo($display->getOption('arguments') ?: []);
        if (!empty($argumentsInfo)) {
          $id = implode('_', [
            $viewId, $displayId, 'view', 'contextual', 'filter', 'input'
          ]);

          $this->derivatives[$id] = [
            'id' => $id,
            'name' => $display->getGraphQLContextualFilterInputName(),
            'fields' => array_fill_keys(array_keys($argumentsInfo), [
              'type' => 'String',
              // Always expose contextual filters as nullable. Let views module
              // decide what to do if value is missing.
              'nullable' => TRUE,
              'multi' => FALSE,
            ]),
            'view' => $viewId,
            'display' => $displayId,
          ] + $this->getCacheMetadataDefinition($view) + $basePluginDefinition;
        }
      }
    }

    return parent::getDerivativeDefinitions($basePluginDefinition);
  }

}
