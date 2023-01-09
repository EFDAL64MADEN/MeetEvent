<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Recherche les évènements en fonction du formulaire
     * @return void
     */
    public function search($mots = null)
    {
        // On contruit la requête DQL via le createQueryBuilder()
        $query = $this->createQueryBuilder('e');
        // Si on a au moins un mot
        if($mots != null){
            // clause qui va nous permettre de chercher le mot dans différentes colonnes de la table event
            // MATCH_AGAINST est conçu spécialement pour permettre la recherche full-text : dans le match on indique les colonnes dans lesquelles on cherche et dans le against le mots recherché
            $query->where('MATCH_AGAINST(e.nameOfEvent, e.zipCode, e.city, e.description)
                           AGAINST(:keyWord boolean)>0')
                // setParameter() protège contre les injections sql 
                ->setParameter('keyWord', $mots);
        }
        // On récupère les résultats de la requête
        return $query->getQuery()->getResult();
    }

    public function add(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    

//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
