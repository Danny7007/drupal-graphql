<?php

namespace Drupal\graphql_core\Plugin\Deriver\Fields;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\graphql\Utility\StringHelper;
use Drupal\graphql_core\Plugin\Deriver\EntityFieldDeriverWithTypeMapping;
use Drupal\graphql_core\Plugin\GraphQL\Fields\Entity\EntityField;
use Drupal\graphql_core\Plugin\GraphQL\Types\Entity\EntityFieldType;

// TODO Write tests for entity reference graph traversal.

// TODO Should we expose config entities?

// TODO Convert timestamps to strings?

/**
 * Deriver for RawValue fields.
 */
class EntityFieldDeriver extends EntityFieldDeriverWithTypeMapping {

  /**
   * {@inheritdoc}
   */
  protected function getDerivativesFromPropertyDefinitions($entityTypeId, FieldStorageDefinitionInterface $definition, array $basePluginDefinition, $bundleId = NULL) {
    $fieldName = $definition->getName();

    $derivative = [
      'types' => [StringHelper::camelCase([$entityTypeId])],
      'name' => EntityField::getId($fieldName),
      'multi' => $definition->isMultiple(),
      'field' => $fieldName,
    ];

    $properties = $definition->getPropertyDefinitions();
    if (count($properties) === 1) {
      // Flatten the structure for single-property fields.
      /** @var \Drupal\Core\TypedData\DataDefinitionInterface $property */
      $property = reset($properties);
      $keys = array_keys($properties);

      $derivative['type'] = $this->typeMapper->typedDataToGraphQLFieldType($property);
      $derivative['property'] = reset($keys);
    }
    else {
      $derivative['type'] = EntityFieldType::getId($entityTypeId, $fieldName);
    }

    $key = is_null($bundleId) ? "$entityTypeId-$fieldName" : "$entityTypeId-$bundleId-$fieldName";

    $this->derivatives[$key] = $derivative + $basePluginDefinition;
  }
}
