<?php
/**
 * @author Emile Camara <camara.emile@gmail.com>
 * @project  defi-fullstack-app
 */
declare(strict_types=1);

namespace App\Security\Infrastructure\Repository;

use App\Security\Domain\Entity\User;
use App\Security\Domain\Repository\UserRepository;
use App\Security\Domain\ValueObject\Email;
use App\Security\Infrastructure\Doctrine\Mapping\DoctrineUser;
use App\Shared\Domain\Exception\EntityPersistenceException;
use App\Shared\Infrastructure\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Throwable;

class MysqlUserRepository extends BaseRepository implements UserRepository
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        LoggerInterface $userLogger
    ) {
        parent::__construct(
            $managerRegistry,
            DoctrineUser::class,
            'User',
            $userLogger
        );
    }
    public function save(User $user): void {
        try {
            $entity = $this->toDoctrineEntity($user);
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
        } catch (Throwable $e) {
            $exception = EntityPersistenceException::fromPrevious(
                $this->entityName,
                $e
            );
            $this->logAndThrowException(
                $exception,
                $user,
                [  'action' => 'save',  'data' => $this->serializeEntity($user)]
            );
        }
    }

    public function findByEmail(Email $email): ?User {
        $user = $this->getEntityManager()
            ->getRepository(DoctrineUser::class)
            ->findOneBy(['email' => $email->value()]);
        if ($user === null) {
            $this->logAndThrowNotFoundException($email->value());
        }

        return $this->toDomainEntity($user);
    }

    private function toDomainEntity(DoctrineUser $user): User {
        return new User(
            $user->id,
            $user->email,
            $user->hashedPassword,
            $user->roles
        );
    }

    private function toDoctrineEntity(User $user): DoctrineUser
    {
        $doctrineUser = new DoctrineUser();
        $doctrineUser->id = $user->id;
        $doctrineUser->email = $user->email;
        $doctrineUser->hashedPassword = $user->hashedPassword;
        $doctrineUser->roles = $user->roles;

        return $doctrineUser;
    }
}
