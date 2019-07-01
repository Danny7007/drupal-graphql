# Querying nodes

A common scenario into which you will run often is to query a node together with certain field. We have already seen a couple of things that can give a good overview on how to do this. Let's take our example of the "Article" type again and query some fields like the `id`, the `label` but also the custom field `creator` which is just a normal text field.

Before you start, make sure to read the introduction and how to add a custom schema, only after that you should start adding resolvers and extending your schema with your own types.

## Add the schema declaration

The first step, as seen in the introduction, is to add the types and fields in the schema. You can add this directly into schema string in your own schema implementation (`src/Plugin/GraphQL/Schema/SdlSchemaMyDrupalGql.php`).

```
schema {
    query: Query
}

type Query {
    ...
    article(id: Int!): Article
    ...
}

type Article implements NodeInterface {
    id: Int!
    title: String!
    creator: String
}

...

interface NodeInterface {
    id: Int!
}
```

Now we have an "Article" type in the schema with three fields `id`, `label` and our custom field `creator`. The next step is to add resolvers for each of them.

## Adding resolvers

To add the resolvers we go to our schema implementation and call the appropriate data producers inside the `getResolverRegistry` method.

```php
/**
   * {@inheritdoc}
   */
  protected function getResolverRegistry() {
    $builder = new ResolverBuilder();
    $registry = new ResolverRegistry([
      'Article' => ContextDefinition::create('entity:node')
        ->addConstraint('Bundle', 'article'),
    ]);

    $registry->addFieldResolver('Query', 'article',
      $builder->produce('entity_load', ['mapping' => [
        'type' => $builder->fromValue('node'),
        'bundles' => $builder->fromValue(['article']),
        'id' => $builder->fromArgument('id'),
      ]])
    );

    $registry->addFieldResolver('Article', 'id',
      $builder->produce('entity_id', ['mapping' => [
        'entity' => $builder->fromParent(),
      ]])
    );

    $registry->addFieldResolver('Article', 'title',
      $builder->produce('entity_label', ['mapping' => [
        'entity' => $builder->fromParent(),
      ]])
    );

    $registry->addFieldResolver('Article', 'creator',
      $builder->produce('property_path', [
        'mapping' => [
          'type' => $builder->fromValue('entity:node'),
          'value' => $builder->fromParent(),
          'path' => $builder->fromValue('field_article_creator.value'),
        ],
      ])
    );

    return $registry;
  }
```

