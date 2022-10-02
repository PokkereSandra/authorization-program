<?php

namespace App\Repositories;

use App\Auth;
use App\Models\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class MySqlUsersRepository implements UsersRepository
{
    private Connection $connection;

    public function __construct()
    {
        $connectionParams = [
            'dbname' => 'users',
            'user' => 'root',
            'password' => '',
            'host' => 'localhost',
            'driver' => 'pdo_mysql',
        ];

        $this->connection = DriverManager::getConnection($connectionParams);
    }

    public function save(User $user): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->insert('users')
            ->values([
                'name' => ':name',
                'email' => ':email',
                'password' => ':password',
                'agreement' => ':agreement',
            ])
            ->setParameters([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
                'agreement' => $user->getAgreement(),
            ])
            ->executeQuery();

        $queryBuilder = $this->connection->createQueryBuilder();
        $user = $queryBuilder->select('id')
            ->from('users')
            ->where('name = :name')
            ->setParameter('name', $user->getName())
            ->fetchAssociative();

        Auth::authorize($user['id']);
    }

    public function getByName(string $name)
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $user = $queryBuilder->select('*')
            ->from('users')
            ->where('name = :name')
            ->setParameter('name', $name)
            ->fetchAssociative();

        if ($user) {
            return new User(
                $user ['name'],
                $user ['email'],
                $user ['password'],
                $user ['agreement'],
                $user ['id']
            );
        }
        return false;
    }

    public function checkNameInDb(string $name): string
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $query = $queryBuilder->select('*')
            ->from('users')
            ->where('name = :name')
            ->setParameter('name', $name)
            ->fetchOne();
        if ($query) {
            return $query;
        }
        return '';
    }

    public function checkEmailInDb(string $email): string
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $query = $queryBuilder->select('*')
            ->from('users')
            ->where('email = :email')
            ->setParameter('email', $email)
            ->fetchOne();
        if ($query) {
            return $query;
        }
        return '';
    }

    public function changeUserData(User $user): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->update('users', 'u')
            ->set('u.name', ':userName')
            ->set('u.email', ':userEmail')
            ->set('u.password', ':userPassword')
            ->where('u.id = :id')
            ->setParameters([
                'id' => $user->getId(),
                'userName' => $user->getName(),
                'userEmail' => $user->getEmail(),
                'userPassword' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
            ])
            ->executeQuery();
    }

    public function checkTokenById($id): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $query = $queryBuilder->select('*')
            ->from('remember_tokens')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $id)
            ->fetchAssociative();
        if (!$query) {
            return [];
        }
        return $query;
    }

    public function saveToken($id, $hash): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->insert('remember_tokens')
            ->values([
                'user_id' => ':user_id',
                'token' => ':token',
            ])
            ->setParameters([
                'user_id' => $id,
                'token' => $hash,
            ])
            ->executeQuery();
    }

    public function getUserIdByToken(string $token): string
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $query = $queryBuilder->select('*')
            ->from('remember_tokens')
            ->where('token = :token')
            ->setParameter('token', $token)
            ->fetchAssociative();

        if ($query) {
            return $query['user_id'];
        }
        return '';
    }

    public function deleteToken(int $id): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->delete('remember_tokens')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $id);
        $queryBuilder->executeQuery();
    }
}
