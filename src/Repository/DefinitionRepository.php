<?php

declare(strict_types=1);

namespace CoolMS\Field\Doctrine\Repository;

use CoolMS\Field\Entity\Definition;
use CoolMS\Field\Entity\DefinitionInterface;
use CoolMS\Field\Repository\DefinitionRepositoryInterface;
use CoolMS\Core\Doctrine\Repository\DoctrineRepository;
use CoolMS\Entity\Repository\OrderableRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @extends DoctrineRepository<DefinitionInterface>
 */
class DefinitionRepository extends DoctrineRepository implements DefinitionRepositoryInterface, OrderableRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, ParameterBagInterface $parameterBag)
    {
        parent::__construct($registry, Definition::class, $parameterBag);
    }

    /**
     * @return DefinitionInterface[]
     */
    public function findByEntityAlias(string $entityAlias): array
    {
        return (array) $this->findBy(['entityAlias' => $entityAlias]);
    }

    public function maxSortOrderByAlias(string $entityAlias): int
    {
        $result = $this->createQueryBuilder('f')
            ->select('MAX(f.sortOrder)')
            ->where('f.entityAlias = :alias')
            ->setParameter('alias', $entityAlias)
            ->getQuery()
            ->getSingleScalarResult();

        return null !== $result ? (int) $result : 0;
    }

    public function reorderBatch(array $idToPosition): void
    {
        if ([] === $idToPosition) {
            return;
        }

        foreach ($idToPosition as $id => $position) {
            $this->createQueryBuilder('f')
                ->update()
                ->set('f.sortOrder', ':pos')
                ->where('f.id = :id')
                ->setParameter('pos', $position)
                ->setParameter('id', $id)
                ->getQuery()
                ->execute();
        }
    }
}
