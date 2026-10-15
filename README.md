# coolms/field-doctrine

The Doctrine half of the field family: the XML mapping for the field
definition, its repository, and the entity field-name resolver the schema tooling
reads.

```bash
composer require coolms/field-doctrine:^2.0@alpha
```

Only pre-releases exist so far, hence the `@alpha` stability flag.

## Why the mapping is XML

`coolms/field` must not import the ORM, so the mapping cannot live beside the
entity. It ships here instead, as `src/mapping/Definition.orm.xml`.

`coolms/field-bundle` registers it for you. Without the bundle, an application's
own extension does what the bundle's does in its `prepend()`:

```php
use CoolMS\Field\Entity\Definition;
use CoolMS\Field\Entity\DefinitionInterface;

$container->prependExtensionConfig('doctrine', [
    'orm' => [
        'resolve_target_entities' => [
            DefinitionInterface::class => Definition::class,
        ],
        'entity_managers' => [
            'central' => [
                'mappings' => [
                    'FieldBundle' => [
                        'is_bundle' => false,
                        'type' => 'xml',
                        'dir' => '%kernel.project_dir%/vendor/coolms/field-doctrine/src/mapping',
                        'prefix' => 'CoolMS\Field\Entity',
                        'alias' => 'Field',
                    ],
                ],
            ],
        ],
    ],
]);
```

`central` is the entity manager the bundle uses; name yours there.
`resolve_target_entities` is what lets code typed against `DefinitionInterface`
reach the mapped class.

**`is_bundle` must be false.** With it true, `dir` resolves against the bundle
directory rather than the path you wrote.

## What is mapped

`CoolMS\Field\Entity\Definition`, to the table `coolms_field_definitions`.

The driver owns the whole `CoolMS\Field\Entity` prefix. An entity added there
with no matching `.orm.xml` is not mapped, and nothing reports it: the class
never becomes an entity. Add the mapping in the same change as the class.

## Repository

`Repository\DefinitionRepository` extends
`CoolMS\Core\Doctrine\Repository\DoctrineRepository` and implements the domain's
`DefinitionRepositoryInterface` and `OrderableRepositoryInterface`. A consuming
application's service scan does not discover it: `coolms/field-bundle` registers
it and aliases the interface.

## Entity field names

`DoctrineEntityFieldNamesResolver::resolve()` answers, as a set keyed by name,
the properties an entity class maps natively: its fields, its associations and
its embeddables. An embeddable appears twice: by the flattened paths
`getFieldNames()` reports (`locale.value`) and by its own name (`locale`), so a
caller can match either. A class no entity manager handles answers an empty set.

## Requires

PHP 8.5, `coolms/core`, `coolms/core-doctrine`, `coolms/entity`, `coolms/field`,
`doctrine/orm`, `doctrine/persistence`, `symfony/dependency-injection`.

## Contributing

See the CoolMS organization's
[contributing guide](https://github.com/coolms/.github/blob/develop/CONTRIBUTING.md).

## License

MIT. See [LICENSE](LICENSE).
