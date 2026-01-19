<?php

declare(strict_types=1);

namespace W33bvgl\GtxTranslate\DTO;

readonly class TranslationResult
{
    public function __construct(
        public readonly string $originalText,
        public readonly string $translatedText,
        public readonly string $sourceLanguage,
        public readonly string $targetLanguage,
    ) {}

    public function toArray(): array
    {
        return [
            'original_text' => $this->originalText,
            'translated_text' => $this->translatedText,
            'source_language' => $this->sourceLanguage,
            'target_language' => $this->targetLanguage,
        ];
    }
}
