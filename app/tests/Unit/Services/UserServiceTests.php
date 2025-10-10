<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Config;
use App\DTO\Mails\Addressee;
use App\DTO\Mails\ResetPasswordMail;
use App\DTO\Mails\VerificationMail;
use App\Models\ForgotPasswordToken;
use App\Models\User;
use App\Models\VerificationToken;
use App\Repositories\Token\TokenRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\MailService;
use App\Services\UserService;
use PHPUnit\Framework\TestCase;

final class UserServiceTests extends TestCase
{
    private UserService $userService;
    // private Config $config;
    private MailService $mailService;
    private UserRepositoryInterface $userRepo;
    private TokenRepositoryInterface $tokenRepo;

    protected function setUp(): void
    {
        // Create config mock
        $config = $this->createMock(Config::class);
        $config = $this->getMockBuilder(Config::class)->setConstructorArgs(
            [ 'env' =>
                [
                    'SENDER_EMAIL' => 'sender@example.com',
                    'SENDER_NAME' => 'Test Sender'
                ]
            ]
        )->getMock();

        // Create mock dependencies
        $this->mailService = $this->createMock(MailService::class);
        $this->userRepo = $this->createMock(UserRepositoryInterface::class);
        $this->tokenRepo = $this->createMock(TokenRepositoryInterface::class);

        // Create service instance
        $this->userService = new UserService(
            $config,
            $this->mailService,
            $this->userRepo,
            $this->tokenRepo
        );
    }

    public function test_register_creates_user_with_hashed_password()
    {
        $email = 'test@example.com';
        $password = 'password123';
        
        $this->userRepo->expects($this->once())
            ->method('save')
            ->willReturnCallback(function(User $user) use ($email) {
                $this->assertEquals($email, $user->getEmail());
                $this->assertStringStartsWith('user', $user->getUsername());
                $this->assertTrue(password_verify('password123', $user->getPasswordHash()));
                return $user;
            });

        // Mock token operations for sendVerificationLink
        $this->tokenRepo->expects($this->once())
            ->method('getTokenByUserId')
            ->willReturn(null);

        $this->tokenRepo->expects($this->once())
            ->method('saveToken')
            ->willReturn(true);

        $this->mailService->expects($this->once())
            ->method('send')
            ->willReturn(true);

        $result = $this->userService->register($email, $password);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($email, $result->getEmail());
    }

    public function test_register_generates_random_username()
    {
        $this->userRepo->method('save')->willReturnArgument(0);
        $this->tokenRepo->method('getTokenByUserId')->willReturn(null);
        $this->tokenRepo->method('saveToken')->willReturn(true);
        $this->mailService->method('send')->willReturn(true);

        $user1 = $this->userService->register('user1@example.com', 'pass123');
        $user2 = $this->userService->register('user2@example.com', 'pass123');

        $this->assertMatchesRegularExpression('/^user\d{3}$/', $user1->getUsername());
        $this->assertMatchesRegularExpression('/^user\d{3}$/', $user2->getUsername());
    }

    public function test_register_sends_verification_link()
    {
        $email = 'test@example.com';
        
        $this->userRepo->method('save')->willReturnArgument(0);
        $this->tokenRepo->method('getTokenByUserId')->willReturn(null);
        $this->tokenRepo->method('saveToken')->willReturn(true);

        $this->mailService->expects($this->once())
            ->method('send')
            ->with(
                $this->isInstanceOf(Addressee::class),
                $this->isInstanceOf(Addressee::class),
                $this->isInstanceOf(VerificationMail::class)
            )
            ->willReturn(true);

        $this->userService->register($email, 'password123');
    }

    public function test_send_verification_link_deletes_existing_token()
    {
        $user = $this->createMockUser('1', 'test@example.com', 'user123');
        $existingToken = $this->createMock(VerificationToken::class);

        $this->tokenRepo->expects($this->once())
            ->method('getTokenByUserId')
            ->with('1', VerificationToken::class)
            ->willReturn($existingToken);

        $this->tokenRepo->expects($this->once())
            ->method('deleteToken')
            ->with($existingToken);

        $this->tokenRepo->expects($this->once())
            ->method('saveToken')
            ->willReturn(true);

        $this->mailService->expects($this->once())
            ->method('send')
            ->willReturn(true);

        $this->userService->sendVerificationLink($user);
    }

    public function test_send_verification_link_creates_new_token()
    {
        $user = $this->createMockUser('1', 'test@example.com', 'user123');

        $this->tokenRepo->expects($this->once())
            ->method('getTokenByUserId')
            ->willReturn(null);

        $this->tokenRepo->expects($this->once())
            ->method('saveToken')
            ->with($this->isInstanceOf(VerificationToken::class))
            ->willReturn(true);

        $this->mailService->expects($this->once())
            ->method('send')
            ->willReturn(true);

        $this->userService->sendVerificationLink($user);
    }

    public function test_send_verification_link_sends_email_with_correct_data()
    {
        $user = $this->createMockUser('1', 'test@example.com', 'user123');

        $this->tokenRepo->method('getTokenByUserId')->willReturn(null);
        $this->tokenRepo->method('saveToken')->willReturn(true);

        $this->mailService->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(function(Addressee $sender) {
                    return $sender->address === 'sender@example.com' 
                        && $sender->name === 'Test Sender';
                }),
                $this->callback(function(Addressee $receiver) use ($user) {
                    return $receiver->address === $user->getEmail() 
                        && $receiver->name === $user->getUsername();
                }),
                $this->isInstanceOf(VerificationMail::class)
            )
            ->willReturn(true);

        $result = $this->userService->sendVerificationLink($user);
        $this->assertTrue($result);
    }

    public function test_send_reset_token_link_deletes_existing_token()
    {
        $user = $this->createMockUser('1', 'test@example.com', 'user123');
        $existingToken = $this->createMock(ForgotPasswordToken::class);

        $this->tokenRepo->expects($this->once())
            ->method('getTokenByUserId')
            ->with('1', ForgotPasswordToken::class)
            ->willReturn($existingToken);

        $this->tokenRepo->expects($this->once())
            ->method('deleteToken')
            ->with($existingToken);

        $this->tokenRepo->expects($this->once())
            ->method('saveToken')
            ->willReturn(true);

        $this->mailService->expects($this->once())
            ->method('send')
            ->willReturn(true);

        $this->userService->sendResetTokenLink($user);
    }

    public function test_send_reset_token_link_creates_new_token()
    {
        $user = $this->createMockUser('1', 'test@example.com', 'user123');

        $this->tokenRepo->expects($this->once())
            ->method('getTokenByUserId')
            ->willReturn(null);

        $this->tokenRepo->expects($this->once())
            ->method('saveToken')
            ->with($this->isInstanceOf(ForgotPasswordToken::class))
            ->willReturn(true);

        $this->mailService->expects($this->once())
            ->method('send')
            ->willReturn(true);

        $this->userService->sendResetTokenLink($user);
    }

    public function test_send_reset_token_link_sends_email_with_correct_data()
    {
        $user = $this->createMockUser('1', 'test@example.com', 'user123');

        $this->tokenRepo->method('getTokenByUserId')->willReturn(null);
        $this->tokenRepo->method('saveToken')->willReturn(true);

        $this->mailService->expects($this->once())
            ->method('send')
            ->with(
                $this->isInstanceOf(Addressee::class),
                $this->isInstanceOf(Addressee::class),
                $this->isInstanceOf(ResetPasswordMail::class)
            )
            ->willReturn(true);

        $result = $this->userService->sendResetTokenLink($user);
        $this->assertTrue($result);
    }

    public function test_verify_user_with_valid_token()
    {
        $rawToken = 'valid-token-123';
        $tokenHash = 'hashed-token';
        
        $user = $this->createMockUser('1', 'test@example.com', 'user123');
        $token = $this->createMock(VerificationToken::class);
        $token->method('isExpired')->willReturn(false);

        // Mock static method
        $this->mockStaticMethod(VerificationToken::class, 'generateTokenHash', $tokenHash);

        $this->tokenRepo->expects($this->once())
            ->method('getUserAndToken')
            ->with($tokenHash, VerificationToken::class)
            ->willReturn(['user' => $user, 'token' => $token]);

        $verifiedUser = clone $user;
        $this->userRepo->expects($this->once())
            ->method('verifyUser')
            ->with($user)
            ->willReturn($verifiedUser);

        $this->tokenRepo->expects($this->once())
            ->method('deleteToken')
            ->with($token);

        $result = $this->userService->verifyUser($rawToken);

        $this->assertInstanceOf(User::class, $result);
    }

    public function test_verify_user_returns_null_when_token_not_found()
    {
        $rawToken = 'invalid-token';
        $tokenHash = 'hashed-token';

        $this->mockStaticMethod(VerificationToken::class, 'generateTokenHash', $tokenHash);

        $this->tokenRepo->expects($this->once())
            ->method('getUserAndToken')
            ->willReturn(null);

        $result = $this->userService->verifyUser($rawToken);

        $this->assertNull($result);
    }

    public function test_verify_user_returns_null_when_token_expired()
    {
        $rawToken = 'expired-token';
        $tokenHash = 'hashed-token';
        
        $user = $this->createMockUser('1', 'test@example.com', 'user123');
        $token = $this->createMock(VerificationToken::class);
        $token->method('isExpired')->willReturn(true);

        $this->mockStaticMethod(VerificationToken::class, 'generateTokenHash', $tokenHash);

        $this->tokenRepo->expects($this->once())
            ->method('getUserAndToken')
            ->willReturn(['user' => $user, 'token' => $token]);

        $this->userRepo->expects($this->never())->method('verifyUser');
        $this->tokenRepo->expects($this->never())->method('deleteToken');

        $result = $this->userService->verifyUser($rawToken);

        $this->assertNull($result);
    }

    public function test_validate_verification_token_returns_true_for_valid_token()
    {
        $token = 'valid-token';

        $this->tokenRepo->expects($this->once())
            ->method('checkToken')
            ->with($token, VerificationToken::class)
            ->willReturn(true);

        $result = $this->userService->validateVerificationToken($token);

        $this->assertTrue($result);
    }

    public function test_validate_verification_token_returns_false_for_invalid_token()
    {
        $token = 'invalid-token';

        $this->tokenRepo->expects($this->once())
            ->method('checkToken')
            ->with($token, VerificationToken::class)
            ->willReturn(false);

        $result = $this->userService->validateVerificationToken($token);

        $this->assertFalse($result);
    }

    public function test_validate_forgot_password_token_returns_true_for_valid_token()
    {
        $token = 'valid-token';

        $this->tokenRepo->expects($this->once())
            ->method('checkToken')
            ->with($token, ForgotPasswordToken::class)
            ->willReturn(true);

        $result = $this->userService->validateForgotPasswordToken($token);

        $this->assertTrue($result);
    }

    public function test_validate_forgot_password_token_returns_false_for_invalid_token()
    {
        $token = 'invalid-token';

        $this->tokenRepo->expects($this->once())
            ->method('checkToken')
            ->with($token, ForgotPasswordToken::class)
            ->willReturn(false);

        $result = $this->userService->validateForgotPasswordToken($token);

        $this->assertFalse($result);
    }

    public function test_reset_password_with_valid_token()
    {
        $rawToken = 'valid-reset-token';
        $tokenHash = 'hashed-reset-token';
        $newPassword = 'newPassword123';
        
        $user = $this->createMockUser('1', 'test@example.com', 'user123');
        $token = $this->createMock(ForgotPasswordToken::class);
        $token->method('isExpired')->willReturn(false);

        $this->mockStaticMethod(ForgotPasswordToken::class, 'generateTokenHash', $tokenHash);

        $this->tokenRepo->expects($this->once())
            ->method('getUserAndToken')
            ->with($tokenHash, ForgotPasswordToken::class)
            ->willReturn(['user' => $user, 'token' => $token]);

        $this->userRepo->expects($this->once())
            ->method('updatePassword')
            ->with(
                $user,
                $this->callback(function($passwordHash) use ($newPassword) {
                    return password_verify($newPassword, $passwordHash);
                })
            )
            ->willReturn($user);

        $this->tokenRepo->expects($this->once())
            ->method('deleteToken')
            ->with($token);

        $result = $this->userService->resetPassword($rawToken, $newPassword);

        $this->assertTrue($result);
    }

    public function test_reset_password_returns_false_when_token_not_found()
    {
        $rawToken = 'invalid-token';
        $tokenHash = 'hashed-token';
        $newPassword = 'newPassword123';

        $this->mockStaticMethod(ForgotPasswordToken::class, 'generateTokenHash', $tokenHash);

        $this->tokenRepo->expects($this->once())
            ->method('getUserAndToken')
            ->willReturn(null);

        $this->userRepo->expects($this->never())->method('updatePassword');
        $this->tokenRepo->expects($this->never())->method('deleteToken');

        $result = $this->userService->resetPassword($rawToken, $newPassword);

        $this->assertFalse($result);
    }

    public function test_reset_password_returns_false_when_token_expired()
    {
        $rawToken = 'expired-token';
        $tokenHash = 'hashed-token';
        $newPassword = 'newPassword123';
        
        $user = $this->createMockUser('1', 'test@example.com', 'user123');
        $token = $this->createMock(ForgotPasswordToken::class);
        $token->method('isExpired')->willReturn(true);

        $this->mockStaticMethod(ForgotPasswordToken::class, 'generateTokenHash', $tokenHash);

        $this->tokenRepo->expects($this->once())
            ->method('getUserAndToken')
            ->willReturn(['user' => $user, 'token' => $token]);

        $this->userRepo->expects($this->never())->method('updatePassword');
        $this->tokenRepo->expects($this->never())->method('deleteToken');

        $result = $this->userService->resetPassword($rawToken, $newPassword);

        $this->assertFalse($result);
    }

    public function test_reset_password_returns_false_when_update_fails()
    {
        $rawToken = 'valid-token';
        $tokenHash = 'hashed-token';
        $newPassword = 'newPassword123';
        
        $user = $this->createMockUser('1', 'test@example.com', 'user123');
        $token = $this->createMock(ForgotPasswordToken::class);
        $token->method('isExpired')->willReturn(false);

        $this->mockStaticMethod(ForgotPasswordToken::class, 'generateTokenHash', $tokenHash);

        $this->tokenRepo->expects($this->once())
            ->method('getUserAndToken')
            ->willReturn(['user' => $user, 'token' => $token]);

        $this->userRepo->expects($this->once())
            ->method('updatePassword')
            ->willReturn(null);

        $this->tokenRepo->expects($this->never())->method('deleteToken');

        $result = $this->userService->resetPassword($rawToken, $newPassword);

        $this->assertFalse($result);
    }

    private function createMockUser(string $id, string $email, string $username): User
    {
        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $reflection = new \ReflectionClass($user);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($user, $id);

        $usernameProperty = $reflection->getProperty('username');
        $usernameProperty->setAccessible(true);
        $usernameProperty->setValue($user, $username);

        $emailProperty = $reflection->getProperty('email');
        $emailProperty->setAccessible(true);
        $emailProperty->setValue($user, $email);

        $passwordHashProperty = $reflection->getProperty('password_hash');
        $passwordHashProperty->setAccessible(true);
        $passwordHashProperty->setValue($user, password_hash('password', PASSWORD_DEFAULT));

        return $user;
    }

    private function mockStaticMethod(string $class, string $method, $returnValue): void
    {
        // For static methods, you'll need to use a workaround or dependency injection
        // This is a placeholder - in real implementation, consider refactoring to inject
        // a TokenHasher service that can be mocked, or use a library like AspectMock
    }
}
