<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return array<Task>
     */
    public function getTasksFinished(): array {
        return $this->createQueryBuilder('t')
                    ->where('t.isDone = true')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return array<Task>
     */
    public function getTasksNotFinished(): array {
        return $this->createQueryBuilder('t')
            ->where('t.isDone = false')
            ->getQuery()
            ->getResult();
    }
}
