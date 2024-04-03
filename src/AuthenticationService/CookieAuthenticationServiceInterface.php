<?php

namespace src\Core\AuthenticationService;

use Entity\User;
use Psr\Log\LoggerInterface;
use Repository\UserRepository;
use function Core\AuthenticationService\cookieId;

class CookieAuthenticationServiceInterface implements AuthenticationServiceInterface
{
    private UserRepository $userRepository;
    private LoggerInterface $logger;

    public function __construct(UserRepository $userRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }
    public function check(): bool
    {
        return isset($_COOKIE['user_id']);
    }

    public function getCurrentUser(): User|null
    {
        if ($this->check()) {
            $userId = $_COOKIE['user_id'];

            return $this->userRepository->getUserById($userId);
        }
        return  null;
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userRepository->getOneByEmail($email);
        if (!$user instanceof User) {
            return false;
        }

        if (!password_verify($password, $user->getPassword())) {
            return false;
        }

        $userId = $user->getId();
        setcookie('user_id', $user->getId(), strtotime("+30 days", '/'));
        $cookieId = cookieId();

        $data = [
            'email' => 'email: ' . $email,
            'user_id' => 'user_id: ' . $userId,
            'Session' => 'session_id: ' . $cookieId
        ];

        $this->logger->info("Authentication successful\n", $data);

        return true;
    }

    public function logout(): void
    {
        setcookie('user_id', '', time() -3600, '/');
    }
}