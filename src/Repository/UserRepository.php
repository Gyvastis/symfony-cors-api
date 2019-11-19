<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
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

    public function createUser(string $email): ?User
    {
        $existingUser = $this->findOneBy([
            'email' => $email,
        ]);

        if ($existingUser) {
            return null;
        }

        $user = new User();

        $user->setEmail($email);
        $user->setRoles(['USER_ROLE']);
        $user->setApiKey(md5(mt_rand()));

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }
}
