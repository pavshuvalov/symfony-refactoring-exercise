<?php

namespace App\Repository;

use App\Entity\Todos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TodosRepository extends ServiceEntityRepository
{
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, Todos::class);
	}

	public function findNext(int $offset, int $limit)
	{
		return $this->createQueryBuilder('t')
			->setFirstResult($offset)
			->setMaxResults($limit)
			->getQuery()
			->getResult();
	}

	public function findNextByCompleted(bool $completed, int $offset, int $limit)
	{
		return $this->createQueryBuilder('t')
			->where('t.completed = :completed')
			->setParameter('completed', $completed ? 1 : 0)
			->setFirstResult($offset)
			->setMaxResults($limit)
			->getQuery()
			->getResult();
	}
}
