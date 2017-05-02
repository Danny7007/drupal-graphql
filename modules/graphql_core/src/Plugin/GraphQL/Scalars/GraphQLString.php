<?php

namespace Drupal\graphql_core\Plugin\GraphQL\Scalars;

use Youshido\GraphQL\Type\Scalar\StringType;

/**
 * Scalar string type.
 *
 * @GraphQLScalar(
 *   name = "String",
 *   data_type = "string"
 * )
 */
class GraphQLString extends StringType {

}
