# coolms/field-doctrine

The Doctrine half of the field family: the XML mapping for the field
definition, its repository, and the entity field-name resolver the schema tooling
reads.

```bash
composer require coolms/field-doctrine
```

## Why the mapping is XML

`coolms/field` must not import the ORM, so the mapping cannot live beside the
entity. It ships here instead, as `src/mapping/Definition.orm.xml`, and a
consuming application points Doctrine at it:

```php
$container->prependExtensionConfig('doctrine', [
    'orm' => ['entity_managers' => ['central' => ['mappings' => [
        'FieldBundle' => [
            'is_bundle' => false,
            'type' => 'xml',
            'dir' => '%kernel.project_dir%/vendor/coolms/field-doctrine/src/mapping',
            'prefix' => 'CoolMS\Field\Entity',
            'alias' => 'Field',
        ],
    ]]]],
]);
```

**`is_bundle` must be false.** With it true, `dir` resolves against the bundle
directory rather than the path you wrote.

`coolms/field-bundle` does this for you.

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

`DoctrineEntityFieldNamesResolver` answers which properties an entity class maps
natively: its fields, its associations and its embeddables. An embeddable is
named as the property the user sees (`locale`), not by the flattened path
`getFieldNames()` reports (`locale.value`).

## Requires

`coolms/core`, `coolms/core-doctrine`, `coolms/entity`, `coolms/field`,
`doctrine/orm`, `doctrine/persistence`, `symfony/dependency-injection`.

## Contributing

See the CoolMS organization's
[contributing guide](https://github.com/coolms/.github/blob/develop/CONTRIBUTING.md).

## License

MIT. See [LICENSE](LICENSE).
