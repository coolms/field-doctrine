# Changelog

All notable changes to `coolms/field-doctrine` are recorded here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
Versioning is described in `CONTRIBUTING.md` -- read it before assuming what a
major number means here.

Every entry in this file was written in the same commit as the change it
describes. Nothing here is reconstructed.

## 2.0.0-alpha2 - 2026-10-07

### Added

- Declares `support` -- `issues` and `source` -- so a page imported from this
  package, and the catalogue, know where a correction is filed. Packagist filled
  the gap from GitHub when the manifest was silent; the declared field is the
  one that holds on any registry.

## 2.0.0-alpha1 - 2026-09-10

**A pre-release. It carries no compatibility promise**, which is the honest
statement of where the platform is: the shape is still moving, and a stable tag
would be a promise that cannot be kept yet.

Composer will not install it under default stability. Set

```json
"minimum-stability": "alpha",
"prefer-stable": true
```

in your root `composer.json`, then:

```
composer require coolms/field-doctrine:^2.0
```

`prefer-stable` keeps every other dependency of yours on its newest stable
release, so this loosening applies to what actually needs it and nothing else.

!! **A per-package stability flag is not enough.** `composer require
coolms/field-doctrine:^2.0@alpha` admits the alpha of the package it names and **nothing
behind it**, so the siblings it pulls in still fail to resolve -- and composer
reports that against the sibling rather than against what you asked for.

### Added

**The Doctrine adapters for `coolms/field`.** Three classes and the XML mapping,
lifted out of the application that used to contain them. This is the package
that lets `coolms/field` stay framework-free: the entity lives there and names
no persistence library, and its mapping lives here -- the arrangement
`coolms/core` and `coolms/entity` already follow.

`DefinitionRepository` extends `coolms/core-doctrine`'s `DoctrineRepository`.
The host points the ORM at `vendor/coolms/field-doctrine/src/mapping`, and the
simplified XML driver keys on the file name.

### Notes for anyone editing the mapping

The mapping declares the entity's own columns only. `id` and `sortOrder` come
from traits carrying `CoolMS\Core\Mapping` attributes, which the trait mapping
driver translates earlier in the chain, so repeating them here would make them
two-source fields.

!! **An XML comment may not contain a double hyphen**, which is this estate's
standard dash. It does not warn: it makes the parse fail and the entity stop
being mapped.

The unique constraint on `(entity_alias, name)` is the find-or-create key.
Without it a duplicate row can be created that nothing will find again,
orphaning it and desynchronising save from delete.

### Fixed before it shipped

`coolms/core-doctrine` and `symfony/dependency-injection` were used by `src/`
and not declared. A clean install could not autoload this package's own
repository; it worked only because the consuming application installed both for
its own reasons -- the sole-holder shape, where a dependency is present because
something else asked for it and gone when that something else stops.

**There are no tests in this package yet.** `phpunit.xml.dist` points at
`tests/` because that is where they go, not because they exist, and the CI
workflow beside it proves nothing until they do.
