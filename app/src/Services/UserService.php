<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\Mails\Addressee;
use App\DTO\Mails\ResetPasswordMail;
use App\DTO\Mails\VerificationMail;
use App\Models\ForgotPasswordToken;
use App\Models\User;
use App\Core\Config;
use App\Models\VerificationToken;
use App\Repositories\Token\TokenRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected Config $config,
        protected MailService $mailService,
        protected UserRepositoryInterface $userRepo,
        protected TokenRepositoryInterface $tokenRepo
    )
    {
    }

    public function register(string $email, string $password): User
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $username = "user" . mt_rand(100, 999);

        $user = new User($username, $email, $passwordHash);

        $this->userRepo->save($user);

        $this->sendVerificationLink($user);

        return $user;
    }

    public function sendVerificationLink(User $user)
    {
        $userId = $user->getId();
        $token = $this->tokenRepo->getTokenByUserId($userId, VerificationToken::class);

        if ($token) {
            $this->tokenRepo->deleteToken($token);
        }

        $token = new VerificationToken($userId);
        $this->tokenRepo->saveToken($token);
        $sender = new Addressee($this->config->mail['sender_email'], $this->config->mail['sender_name']);
        $receiver = new Addressee($user->getEmail(), $user->getUsername());
        $mail = new VerificationMail("Email Verification")->useTemplate(["verification_link" => "http://localhost:9095/verify?token={$token->getRawToken()}"]);
        return $this->mailService->send($sender, $receiver, $mail);
    }

    public function sendResetTokenLink(User $user): bool
    {
        $userId = $user->getId();
        $token = $this->tokenRepo->getTokenByUserId($userId, ForgotPasswordToken::class);

        if ($token) {
            $this->tokenRepo->deleteToken($token);
        }

        $token = new ForgotPasswordToken($userId);
        $this->tokenRepo->saveToken($token);
        $sender = new Addressee($this->config->mail['sender_email'], $this->config->mail['sender_name']);
        $receiver = new Addressee($user->getEmail(), $user->getUsername());
        $mail = new ResetPasswordMail("Reset Password Link")->useTemplate(["reset_link" => "http://localhost:9095/reset-password?token={$token->getRawToken()}"]);
        return $this->mailService->send($sender, $receiver, $mail);
    }

    public function verifyUser(string $token): ?User
    {
        $tokenHash = VerificationToken::generateTokenHash($token);

        ["user" => $user, "token" => $token] = $this->tokenRepo->getUserAndToken($tokenHash, VerificationToken::class);

        if (! $user || ! $token || $token->isExpired()) {
            return null;
        }

        $user = $this->userRepo->verifyUser($user);

        $this->tokenRepo->deleteToken($token);

        return $user;
    }

    public function validateVerificationToken(string $token): bool
    {
        return $this->tokenRepo->checkToken($token, VerificationToken::class);
    }

    public function validateForgotPasswordToken(string $token): bool
    {
        return $this->tokenRepo->checkToken($token, ForgotPasswordToken::class);
    }

    // what bool value means?
    public function resetPassword(string $token, string $newPassword): bool
    {
        $tokenHash = ForgotPasswordToken::generateTokenHash($token);
        ["user" => $user, "token" => $token] = $this->tokenRepo->getUserAndToken($tokenHash, ForgotPasswordToken::class);

        if (! $user || ! $token || $token->isExpired()) {
            return false;
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);


        if ($this->userRepo->updatePassword($user, $passwordHash)) {
            $this->tokenRepo->deleteToken($token);
            return true;
        }

        return false;
    }
}
