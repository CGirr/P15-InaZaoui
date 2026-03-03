<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws Exception
     */
    public function findAdmin(): ?User
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT id FROM \"user\" WHERE roles::text LIKE :role LIMIT 1";
        $result = $conn->executeQuery($sql, ['role' => '%ROLE_ADMIN%']);
        $id = $result->fetchOne();

        return $id ? $this->find($id) : null;
    }

    /**
     * @throws Exception
     */
    public function findGuestsWithMediaCount(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT u.id, u.name, COUNT(m.id) AS media_count
            FROM \"user\" u
            LEFT JOIN media m ON u.id = m.user_id
            WHERE u.roles::text NOT LIKE :role
            AND u.blocked = false
            GROUP BY u.id
            ORDER BY u.name ASC
            ";

        return $conn->executeQuery($sql, ['role' => '%ROLE_ADMIN%'])->fetchAllAssociative();
    }
}
