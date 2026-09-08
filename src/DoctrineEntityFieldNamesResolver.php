<?php

declare(strict_types=1);

namespace CoolMS\Field\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine ORM implementation of EntityFieldNamesResolverInterface.
 *
 * Returns the union of getFieldNames(), getAssociationNames(), and embeddedClasses
 * keys so callers can detect every native property mapped on the entity, including
 * embeddables that getFieldNames() reports under their flattened path
 * (e.g., 'locale.value' rather than the user-facing 'locale').
 */
final readonly class DoctrineEntityFieldNamesResolver implements EntityFieldNamesResolverInterface
{
    public function __construct(
        private ManagerRegistry $registry,
    ) {
    }

    public function resolve(string $class): array
    {
        /** @var EntityManagerInterface|null $em */
        $em = $this->registry->getManagerForClass($class);
        if (null === $em) {
            return [];
        }
        // EntityManagerInterface::getClassMetadata() returns ORM ClassMetadata, which
        // exposes embeddedClasses; the persistence base interface does not.
        $meta = $em->getClassMetadata($class);

        $set = [];
        foreach ($meta->getFieldNames() as $name) {
            $set[$name] = true;
        }
        foreach ($meta->getAssociationNames() as $name) {
            $set[$name] = true;
        }
        foreach (array_keys($meta->embeddedClasses) as $name) {
            $set[$name] = true;
        }

        return $set;
    }
}
