<?php

namespace Drupal\graphql_core\Plugin\GraphQL\Scalars;

use Youshido\GraphQL\Type\Scalar\FloatType;

/**
 * Scalar float type.
 *
 * @GraphQLScalar(
 *   name = "Float",
 *   data_type = "float"
 * )
 */
class GraphQLFloat extends FloatType {

}
