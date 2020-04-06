<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\ConfirmationSender;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

/**
 * @covers \App\Auth\Service\ConfirmationSender
 */
class ConfirmationSenderTest extends TestCase
{
    public function testSuccess(): void
    {
        $to = new Email('user@app.test');
        $token = new Token((string)rand(100000, 999999), new DateTimeImmutable());

        $twig = $this->createMock(Environment::class);
        $twig->expects($this->once())->method('render')->with(
            $this->equalTo('auth/confirm.html.twig'),
            $this->equalTo(['token' => $token]),
        )->willReturn($body = $token->getValue());

        $mailer = $this->createMock(Swift_Mailer::class);
        $mailer->expects($this->once())->method('send')
            ->willReturnCallback(static function (Swift_Message $message) use ($to, $body): int {
                self::assertEquals([$to->getValue() => null], $message->getTo());
                self::assertEquals('Подтверждение регистрации', $message->getSubject());
                self::assertEquals($body, $message->getBody());
                self::assertEquals('text/html', $message->getBodyContentType());
                return 1;
            });

        $sender = new ConfirmationSender($mailer, $twig);

        $sender->send($to, $token);
    }

    public function testError(): void
    {
        $to = new Email('user@app.test');
        $token = new Token((string)rand(100000, 999999), new DateTimeImmutable());

        $twig = $this->createStub(Environment::class);
        $twig->method('render')->willReturn($token->getValue());

        $mailer = $this->createStub(Swift_Mailer::class);
        $mailer->method('send')->willReturn(0);

        $sender = new ConfirmationSender($mailer, $twig);

        $this->expectException(RuntimeException::class);
        $sender->send($to, $token);
    }
}
