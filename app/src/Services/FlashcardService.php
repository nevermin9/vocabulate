<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Flashcard;

final class FlashcardService
{
    public function addNewFlashcard(
        int $stackId,
        string $word,
        string $translation,
        string $exampleUsage,
        string $exampleUsageTranslation
    ): Flashcard
    {
        $userId = AuthService::getUserId();
        $flashcard = new Flashcard([
            'stack-id' => $stackId,
            'user-id' => $userId,
            'word' => $word,
            'translation' => $translation,
            'example-usage' => $exampleUsage,
            'example-usage-translation' => $exampleUsageTranslation
        ])->create();

        return $flashcard;
    }
}
