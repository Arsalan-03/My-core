<?php

namespace src\Core\AuthenticationService;

use Entity\User;
use Psr\Log\LoggerInterface;
use Repository\UserRepository;

class SessionAuthenticationServiceInterface implements AuthenticationServiceInterface
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
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser(): User|null
    {
        if ($this->check()) {
            $userId = $_SESSION['user_id'];

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

        session_start();
        $userId = $user->getId();
        $_SESSION['user_id'] = $userId;
        $sessionId = session_id();

        $data = [
            'email' => 'email: ' . $email,
            'user_id' => 'user_id: ' . $userId,
            'session' => 'session: ' . $sessionId
        ];

        $this->logger->info("Authentication successful\n", $data);

        return true;
    }

    public function logout(): void
    {
        session_start();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}