<?php

declare(strict_types=1);

namespace App\Identity\Application;

use App\Identity\Application\DTOs\CreateUserDTO;
use App\Identity\Application\DTOs\UpdateUserDTO;
use App\Identity\Domain\Email;
use App\Identity\Domain\User;
use App\Identity\Domain\UserRepository;
use App\Identity\Domain\UserRole;
use App\Shared\Infrastructure\Security\BcryptPasswordHasher;
use DateTimeImmutable;
use InvalidArgumentException;

final class UserService
{
    public function __construct(
        private UserRepository $users,
        private BcryptPasswordHasher $passwordHasher
    ) {
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->users->findByEmail($email);

        if ($user === null || !$user->isActive()) {
            return null;
        }

        if (!$this->passwordHasher->verify($password, $user->passwordHash())) {
            return null;
        }

        return $user;
    }

    /** @return list<User> */
    public function listUsers(): array
    {
        return $this->users->all();
    }

    /** @return list<User> */
    public function listTrainers(): array
    {
        return $this->users->trainers();
    }

    /** @return list<User> */
    public function athletesFor(User $viewer): array
    {
        return match ($viewer->role()) {
            UserRole::ADMIN => $this->users->activeAthletes(),
            UserRole::TRAINER => $this->users->athletesForTrainer($viewer->id() ?? 0),
            UserRole::USER => [$viewer],
        };
    }

    public function findUser(int $id): ?User
    {
        return $this->users->findById($id);
    }

    public function create(CreateUserDTO $dto): User
    {
        $email = new Email($dto->email);

        if ($this->users->findByEmail($email->value()) !== null) {
            throw new InvalidArgumentException('El correo ya está registrado.');
        }

        $role = UserRole::from($dto->role);
        $trainerId = $this->normalizeTrainerId($role, $dto->trainerId);

        if (mb_strlen($dto->password) < 8) {
            throw new InvalidArgumentException('La contraseña debe tener al menos 8 caracteres.');
        }

        $this->assertTrainerExists($trainerId);

        $now = new DateTimeImmutable();
        $user = new User(
            null,
            $dto->name,
            $email,
            $this->passwordHasher->hash($dto->password),
            $role,
            $trainerId,
            true,
            $now,
            $now
        );

        return $this->users->save($user);
    }

    public function update(int $id, UpdateUserDTO $dto): void
    {
        $existingUser = $this->users->findById($id);

        if ($existingUser === null) {
            throw new InvalidArgumentException('El usuario solicitado no existe.');
        }

        $email = new Email($dto->email);
        $userWithSameEmail = $this->users->findByEmail($email->value());

        if ($userWithSameEmail !== null && $userWithSameEmail->id() !== $id) {
            throw new InvalidArgumentException('El correo ya está registrado por otro usuario.');
        }

        $roleValue = $dto->role ?? $existingUser->role()->value;
        $role = UserRole::from($roleValue);
        $trainerId = $this->normalizeTrainerId($role, $dto->trainerId);
        $this->assertTrainerExists($trainerId);

        $existingUser->rename($dto->name);
        $existingUser->changeEmail($email);
        $existingUser->changeRole($role, $trainerId);

        $password = trim($dto->password ?? '');
        if ($password !== '') {
            if (mb_strlen($password) < 8) {
                throw new InvalidArgumentException('La contraseña debe tener al menos 8 caracteres.');
            }

            $existingUser->changePassword($this->passwordHasher->hash($password));
        }

        $existingUser->activate();
        $this->users->update($existingUser);
    }

    public function deactivate(int $id): void
    {
        $user = $this->users->findById($id);

        if ($user === null) {
            throw new InvalidArgumentException('El usuario solicitado no existe.');
        }

        $user->deactivate();
        $this->users->update($user);
    }

    private function normalizeTrainerId(UserRole $role, mixed $trainerId): ?int
    {
        if ($role !== UserRole::USER) {
            return null;
        }

        if ($trainerId === null || $trainerId === '' || (int) $trainerId === 0) {
            return null;
        }

        return (int) $trainerId;
    }

    private function assertTrainerExists(?int $trainerId): void
    {
        if ($trainerId === null) {
            return;
        }

        $trainer = $this->users->findById($trainerId);

        if ($trainer === null || $trainer->role() !== UserRole::TRAINER || !$trainer->isActive()) {
            throw new InvalidArgumentException('Debes seleccionar un entrenador válido.');
        }
    }
}
