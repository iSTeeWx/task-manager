<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findPaginatedAndSortedTasks(int $page, ?string $status = null, ?string $search = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('t');

        // Filter by status
        if ($status) {
            $queryBuilder->andWhere('t.status = :status')
                         ->setParameter('status', $status);
        }

        // Search by title or description
        if ($search) {
            $queryBuilder->andWhere('t.title LIKE :search OR t.description LIKE :search')
                         ->setParameter('search', '%' . $search . '%');
        }

        // Ascending sort based on status
        $queryBuilder->orderBy('t.status', 'ASC');

        // Pagination
        $queryBuilder->setFirstResult(($page - 1) * 10)
                     ->setMaxResults(10);

        return new Paginator($queryBuilder);
    }
}
