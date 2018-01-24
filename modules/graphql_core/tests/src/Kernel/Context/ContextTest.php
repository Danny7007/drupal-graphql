<?php

namespace Drupal\Tests\graphql_core\Kernel\Context;

use Drupal\simpletest\ContentTypeCreationTrait;
use Drupal\simpletest\NodeCreationTrait;
use Drupal\Tests\graphql\Kernel\GraphQLFileTestBase;

/**
 * Test plugin based schema generation.
 *
 * @group graphql_core
 */
class ContextTest extends GraphQLFileTestBase {
  use ContentTypeCreationTrait;
  use NodeCreationTrait;

  public static $modules = [
    'graphql_core',
    'graphql_context_test',
  ];

  /**
   * Test if the schema is created properly.
   */
  public function testSimpleContext() {
    $values = $this->executeQueryFile('context.gql');
    $this->assertEquals([
      'a' => ['name' => 'graphql_context_test.a'],
      'b' => ['name' => 'graphql_context_test.b'],
    ], $values['data']);
  }

}
