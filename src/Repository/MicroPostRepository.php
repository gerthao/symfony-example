<?php

namespace App\Repository;

use App\Entity\MicroPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MicroPost>
 *
 * @method MicroPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method MicroPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method MicroPost[]    findAll()
 * @method MicroPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MicroPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array{total: int, data: MicroPost[]}
     */
    public function list(int $page, int $limit = 10): array
    {
        $query = $this->createQueryBuilder('m')
            ->orderBy('m.created', 'desc');

        $paginator = new Paginator($query, false);
        $total     = count($paginator);
        $data      = $paginator
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getResult();

        return [
            'total' => $total,
            'data'  => $data
        ];
    }

    public function findWithComments(int $id): ?MicroPost
    {
        $value = $this->createQueryBuilder('micro_post')
            ->where("micro_post.id = $id")
            ->addSelect('comments')
            ->leftJoin('micro_post.comments', 'comments')
            ->orderBy('micro_post.created', 'DESC')
            ->getQuery()
            ->getResult();

        if (sizeof($value) === 0) return null;
        else return $value[0];
    }

    public function findAllWithComments(): array
    {
        return $this->createQueryBuilder('micro_post')
            ->addSelect('comments')
            ->leftJoin('micro_post.comments', 'comments')
            ->orderBy('micro_post.created', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function add(MicroPost $microPost, bool $flush = false): void
    {
        $this->getEntityManager()->persist($microPost);

        if ($flush) $this->getEntityManager()->flush();
    }

//    /**
//     * @return MicroPost[] Returns an array of MicroPost objects
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

//    public function findOneBySomeField($value): ?MicroPost
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
