<?php
declare(strict_types=1);

namespace App\DTO\Mails;

use App\DTO\Mails\Mail;

abstract class AbstractMail implements MailInterface
{
    public function __construct(
        protected Mail $type,
        protected string $subject,
        protected string $body = ''
    )
    {
    }

    protected static function getTemplatePath(): string
    {
        return dirname(__DIR__) . "/../MailTemplates/";
    }

    public function isHTML(): bool
    {
        return $this->type === Mail::Html;
    }

    public function isPlain(): bool
    {
        return $this->type === Mail::Plain;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function useTemplate(array $params = []): static
    {
        $templatepath = static::getTemplatePath();

        if (!file_exists($templatepath)) {
            throw new \Exception("template for mail doesn't exist!");
        }

        $content = file_get_contents($templatepath);

        if ($params) {
            foreach ($params as $key => $value) {
                $content = str_replace("{{{$key}}}", $value, $content);
            }
        }

        $this->body = $content;

        return $this;
    }
}
