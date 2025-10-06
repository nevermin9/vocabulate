<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Application;
use App\DTO\Mails\Addressee;
use App\DTO\Mails\ResetPasswordMail;
use App\DTO\Mails\Mail;
use App\DTO\Mails\VerificationMail;
use App\Models\Associations\UserTokenAssociation;
use App\Models\ForgotPasswordToken;
use App\Models\User;
use App\Models\VerificationToken;

final class UserService
{
    public function __construct(private ?User $user = null)
    {
    }
    
    public function register(string $email, string $password): User
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $username = "user" . mt_rand(100, 999);

        $this->user = new User($username, $email, $passwordHash);

        $this->user->save();

        $this->sendVerificationLink();

        return $this->user;
    }

    public function sendVerificationLink()
    {
        $userId = $this->user->getId();
        $token = VerificationToken::getByUserId($userId);

        if ($token) {
            $token->delete();
        }

        $config = Application::config();
        $token = new VerificationToken($userId);
        $token->save();
        $sender = new Addressee($config->mail['sender_email'], $config->mail['sender_name']);
        $receiver = new Addressee($this->user->getEmail(), $this->user->getUsername());
        $mail = new VerificationMail("Email Verification")->useTemplate(["verification_link" => "http://localhost:9095/verify?token={$token->getRawToken()}"]);
        return new MailService()->send($sender, $receiver, $mail);
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
        $mail = new ResetPasswordMail("Reset Password Link")->useTemplate(["reset_link" => "http://localhost:9095/reset-password?token={$token->getRawToken()}"]);
        $isSent = new MailService()->send($sender, $receiver, $mail);
        return $isSent;
    }

    /**
     * Check whether Verification or ForgotPassword token exists and is not expired
     *
     * @template T of AbstractToken
     * @param string $rawToken The raw token string from the request
     * @param class-string<T> $tokenClass The fully qualified class name of the token model (e.g., ForgotPasswordToken::class).
     * @return bool
     */
    public function checkToken(string $rawToken, string $tokenClass): bool
    {
        /** @var T $tokenClass */
        $tokenHash  = $tokenClass::generateTokenHash($rawToken);
        $token = $tokenClass::getByTokenHash($tokenHash);

        if (! $token) {
            return false;
        }

        $isExpired = $token->isExpired();

        if ($isExpired) {
            $token->delete();
        }

        return ! $isExpired;
    }

    public function verifyUser(string $token): ?User
    {
        $tokenHash = VerificationToken::generateTokenHash($token);
        ["user" => $user, "token" => $token] = UserTokenAssociation::getByUserAndTokenHash($tokenHash, VerificationToken::class);

        if (! $user || ! $token || $token->isExpired()) {
            return null;
        }

        $this->user = $user->update(["verified" => 1]);
        $token->delete();

        return $this->user;
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
