<?php

namespace App\Repository;

use App\Entity\InscriptionPayment;
use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InscriptionPayment>
 */
class InscriptionPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InscriptionPayment::class);
    }

    public function findLatestByDateMatch(Member $member): ?InscriptionPayment
    {
        return $this->createQueryBuilder('ip')
            ->innerJoin('ip.enrollee', 'm') // Jointure avec Member
            ->andWhere('ip.createdAt = m.deliveredAt') // Condition d'égalité des dates
            ->andWhere('m.id = :memberId') // Filtrer par membre
            ->setParameter('memberId', $member->getId(),'uuid') // Passer l'ID du membre en paramètre
            ->orderBy('ip.createdAt', 'DESC') // Trier du plus récent au plus ancien
            ->setMaxResults(1) // Ne récupérer qu'un seul résultat
            ->getQuery()
            ->getOneOrNullResult(); // Retourne un seul résultat ou null si aucun trouvé
    }



//    /**
//     * @return InscriptionPayment[] Returns an array of InscriptionPayment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InscriptionPayment
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
