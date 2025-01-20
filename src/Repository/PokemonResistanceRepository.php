<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Card;
use App\Entity\PokemonResistance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PokemonResistance>
 */
final class PokemonResistanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PokemonResistance::class);
    }

    /**
     * @return PokemonResistance[] Returns an array of PokemonResistance objects
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

    //    public function findOneBySomeField($value): ?PokemonResistance
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
