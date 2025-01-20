<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Card;
use App\Entity\PokemonWeakness;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PokemonWeakness>
 */
final class PokemonWeaknessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PokemonWeakness::class);
    }

    /**
     * @return PokemonWeakness[] Returns an array of PokemonWeakness objects
     */
    public function findByCardAndTypeAndValue(Card $card, string $type, string $value): array
    {
        /* @phpstan-ignore return.type */
        return $this->createQueryBuilder('p')
            ->leftJoin('p.cards', 'c')
            ->andWhere('c.id = :card_id')
            ->setParameter('card_id', $card->getId())
            ->leftJoin('p.type', 't')
            ->andWhere('t.name = :type')
            ->setParameter('type', $type)
            ->andWhere('p.value = :value')
            ->setParameter('value', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    //    public function findOneBySomeField($value): ?PokemonWeakness
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
