<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @extends ServiceEntityRepository<Theme>
 *
 * @method Theme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Theme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Theme[]    findAll()
 * @method Theme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $uploadsPath)
    {
        parent::__construct($registry, Theme::class);
        $this->uploadsPath = $uploadsPath;
    }

    public function add(Theme $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Theme $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function uploadThemeLogo(UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadsPath.'/theme_logo';
                
        $originalFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFileName = $originalFileName.'-'.uniqid().'.'.$uploadedFile->guessExtension();
        
        $uploadedFile->move(
            $destination,
            $newFileName
        );

        return $newFileName;
    }

    /**
     * méthode qui va permettre de retrouver les évènements d'un thème
     */
    public function findEventsOfTheme(int $id)
    {
        // On fait appel à l'entityManager
        $entityManager = $this->getEntityManager();
        // On crée la requête
        $req = $entityManager->createQueryBuilder()
                             // On sélectionne l'entité Event
                             ->select('e')
                             // Depuis la liste des entités, on lui donne un alias
                             ->from('App\Entity\Event', 'e')
                             // On fait la jointure avec la propriété "theme" de l'entité
                             ->innerJoin('e.theme', 't')
                             // Clause pour indiquer l'id, sans valeur (placeholder)
                             ->where('t.id = :id')
                             // Remplace le placeholder par la vraie valeur (pas encore exécuté, uniquement stocké)
                             // Permet d'éviter l'injection SQL
                             ->setParameter('id', $id)
                             // On trie les évènements du plus proche au plus lointain (date)
                             ->orderBy('e.startEvent')
                             // On récupère les résultats de la requête
                             ->getQuery()
                             ->getResult();
        // On retourne les résultats de la requête
        return $req;
    }

//    /**
//     * @return Theme[] Returns an array of Theme objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Theme
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
