<?php

declare(strict_types=1);

namespace CoolMS\Field\Doctrine;

/**
 * Resolves the set of native field, association, and embeddable names
 * for an ORM-managed entity class.
 *
 * Abstracts ManagerRegistry / EntityManagerInterface access so that callers
 * outside Infrastructure\Doctrine do not need to import Doctrine types
 * (project architecture rule: Doctrine usage is confined to Infrastructure\Doctrine).
 */
interface EntityFieldNamesResolverInterface
{
    /**
     * @param class-string $class
     *
     * @return array<string, true> name => true set; empty when $class is unmanaged
     */
    public function resolve(string $class): array;
}
