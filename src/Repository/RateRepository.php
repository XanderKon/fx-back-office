<?php

namespace App\Repository;

use App\Entity\Rate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rate>
 *
 * @method Rate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rate[]    findAll()
 * @method Rate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rate::class);
    }

    public function save(Rate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Rate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByBaseAndTargetAndSource(string $base, string $target, int $source): ?Rate
    {
        return $this->findOneBy([
            'base' => $base,
            'target' => $target,
            'source' => $source,
        ]);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function findAllRateWithActiveSource(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.base', 'r.target', 'r.rate', 's.title')
            ->innerJoin('r.source', 's')
            ->where('s.is_active = true')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array<int, string>
     */
    public function findAllAvailableCurrencies(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        try {
            $query = $conn->prepare('
                SELECT DISTINCT 
                    currency
                FROM 
                    (SELECT unnest(array[rate.base, rate.target]) FROM rate) AS t(currency)
                WHERE
                    currency IS NOT NULL
                ORDER BY 
                    currency;'
            );

            return $query
                ->executeQuery()
                ->fetchFirstColumn();
        } catch (Exception $e) {
            return [];
        }
    }
}
