<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Application;
use App\DTO\Mails\Addressee;
use App\DTO\Mails\ResetPasswordMail;
use App\DTO\Mails\Mail;
use App\Models\Associations\UserTokenAssociation;
use App\Models\ForgotPasswordToken;
use App\Models\User;

final class UserService
{
    public function __construct(private ?User $user = null)
    {
    }
    
    public function register(string $email, string $password): User
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $username = "user" . mt_rand(100, 999);

        $user = new User($username, $email, $passwordHash);

        $user->save();

        return $user;
    }

    public function forgotPassword(): bool
    {
        if (! $this->user) {
            throw new \Exception("user instance is null");
        }

        $userId = $this->user->getId();
        $token = ForgotPasswordToken::getByUserId($userId);

        if ($token) {
            $token->delete();
        }

        $config = Application::config();
        $token = new ForgotPasswordToken($userId);
        $token->save();
        $sender = new Addressee($config->mail['sender_email'], $config->mail['sender_name']);
        $receiver = new Addressee($this->user->getEmail(), $this->user->getUsername());
        $mail = new ResetPasswordMail(Mail::Html, "Reset Password Link")->useTemplate(["reset_link" => "http://localhost:9095/reset-password?token={$token->getRawToken()}"]);
        $isSent = new MailService()->send($sender, $receiver, $mail);
        return $isSent;
    }

    public function checkResetPasswordToken(string $rawToken): bool
    {
        $tokenHash = ForgotPasswordToken::generateTokenHash($rawToken);
        $token = ForgotPasswordToken::getByTokenHash($tokenHash);

        if (! $token) {
            return false;
        }

        $isExpired = $token->isExpired();

        if ($isExpired) {
            $token->delete();
        }

        return ! $isExpired;
    }

    // what bool value means?
    public function resetPassword(string $token, string $newPassword): bool
    {
        $tokenHash = ForgotPasswordToken::generateTokenHash($token);
        ["user" => $user, "token" => $token] = UserTokenAssociation::getByUserAndTokenHash($tokenHash, ForgotPasswordToken::class);

        if (! $user || ! $token || $token->isExpired()) {
            return false;
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($user->update(['password_hash' => $passwordHash])) {
            $token->delete();
            return true;
        }

        return false;
    }
}
