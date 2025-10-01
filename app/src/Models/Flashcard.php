<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Application;

final class Flashcard
{
    public readonly int $id;
    public readonly string $word;
    public readonly string $translation;
    public readonly ?string $exampleUsage;
    public readonly ?string $exampleUsageTranslation;
    public readonly string $createdAt;
    public readonly int $stackId;
    public readonly string $userId;

    public function __construct(array $data)
    {
        $this->word = $data['word'];
        $this->translation = $data['translation'];
        $this->exampleUsage = $data['example-usage'] ?? null;
        $this->exampleUsageTranslation = $data['example-usage-translation'] ?? null;
        $this->stackId = $data['stack-id'];
        $this->userId = $data['user-id'];
    }

    public function save(): ?Flashcard
    {
        $db = Application::db();

        $cols = [
            "user_id", "stack_id", "word",
            "translation", "example_usage", "example_usage_translation",
            "created_at"
        ];
        $placeholders = $cols;
        array_pop($placeholders);

        $stmt = $db->prepare(
            "INSERT INTO flashcards (" . implode(", ", $cols) . ") VALUES (:" . implode(", :", $placeholders) . ", NOW());"
        );

        $values = [
            'user_id' => $this->userId,
            'stack_id' => $this->stackId,
            'word' => $this->word,
            'translation' => $this->translation,
            'example_usage' => $this->exampleUsage,
            'example_usage_translation' => $this->exampleUsageTranslation,
        ];

        $ok = $stmt->execute($values);

        if (!$ok) {
            return null;
        }

        $id = (int) $db->lastInsertId();
        $this->id = $id;
        $stmt = $db->prepare("SELECT created_at FROM flashcards WHERE id = :id;");
        $stmt->execute(["id" => $id]);
        $this->createdAt = $stmt->fetchColumn();

        return $this;
    }

    public static function getAllByStack(int $stackId): array
    {
        $db = Application::db();
        $cols = [
            "id", "user_id", "stack_id", "word",
            "translation", "example_usage", "example_usage_translation",
            "created_at"
        ];
        $stmt = $db->prepare("SELECT " . implode(", ", $cols) . " FROM flashcards WHERE stack_id = :stack_id;");
        $ok = $stmt->execute(["stack_id" => $stackId]);

        if (! $ok) {
            return [];
        }

        return $stmt->fetchAll();
    }
}
