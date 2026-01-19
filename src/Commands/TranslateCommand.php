<?php

declare(strict_types=1);

namespace W33bvgl\GtxTranslate\Commands;

use Illuminate\Console\Command;
use W33bvgl\GtxTranslate\Contracts\TranslatorInterface;
use W33bvgl\GtxTranslate\Enums\Language;

class TranslateCommand extends Command
{
    protected $signature = 'gtx:translate {text?} {--to=} {--from=}';

    protected $description = 'Translate text using Google GTX API with interactive mode';

    public function handle(TranslatorInterface $translator): int
    {
        $text = $this->argument('text');
        if (! $text) {
            $text = $this->ask('What text would you like to translate?');
        }

        if (! $text) {
            $this->warn('Text is required to proceed.');

            return self::FAILURE;
        }

        $availableLanguages = Language::values();

        $from = $this->option('from');
        if (! $from) {
            $from = $this->choice(
                'Select source language',
                $availableLanguages,
                'auto'
            );
        }

        $to = $this->option('to');
        if (! $to) {
            $to = $this->choice(
                'Select target language',
                array_filter($availableLanguages, fn ($l) => $l !== 'auto'),
                'en'
            );
        }

        $this->newLine();
        $this->components->info("Translating to [{$to}]...");

        try {
            $result = $translator->translate($text, $to, $from);

            $this->newLine();
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Original Language', "<comment>{$result->sourceLanguage}</comment>"],
                    ['Target Language', "<info>{$result->targetLanguage}</info>"],
                    ['Original Text', $result->originalText],
                    ['Translated Text', $result->translatedText],
                ]
            );
            $this->newLine();

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->components->error('Translation failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
