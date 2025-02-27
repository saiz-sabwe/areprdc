<?php

namespace App\Repository;

use App\Entity\InscriptionPayment;
use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Member>
 */
class MemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    public function findMemberExpired()
    {

        return $this->createQueryBuilder('m')
            ->andWhere('m.expiredAt < :now OR m.expiredAt IS NULL')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('m.id', 'ASC');
    }

    public function findMembersNotLinked()
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.user', 'u')
            ->where('u.id IS NULL');
    }


//    /**
//     * @return Member[] Returns an array of Member objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Member
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
