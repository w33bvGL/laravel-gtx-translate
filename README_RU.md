# Google-Translate-Scraper

**Google-Translate-Scraper** — это PHP Laravel библиотека, предназначенная для скрапинга Google Translate с целью перевода текста между различными языками. Она предоставляет простой интерфейс для выполнения переводов.

Этот пакет позволяет переводить текст с одного языка на другой, скрапируя Google Translate. Он решает такие задачи, как настройка таймаутов запросов, прокси и поддержка множества языков. Это решение для разработчиков, которым нужно автономное средство перевода без необходимости платить за использование API-ключей или полагаться на сторонние сервисы.

## Особенности
- **Скрапинг Google Translate**: Переводите текст, скрапя скрытый API Google Translate.
- **Настраиваемые таймауты**: Установите минимальные и максимальные таймауты для задержки запросов.
- **Поддержка прокси**: Используйте прокси для скрапинга, чтобы избежать ограничений по частоте запросов и блокировок.
- **Поддерживаемые языки**: Легко переводите между несколькими языками, поддерживаемыми Google Translate.
- **Настраиваемые User Agents**: Эмулируйте различные браузеры, чтобы избежать обнаружения.

## Установка
Вы можете установить библиотеку **google-translate-scraper** с помощью Composer. Для этого выполните следующую команду в вашем терминале:

```bash
composer require anidzen/google-translate-scraper
```

## Конфигурация
После установки библиотеки необходимо настроить её параметры в конфигурационном файле `google-translate-scraper.php`. В файле вы можете указать следующие параметры:

- **base_url**: URL для основного ресурса.
- **hidden_api_base_url**: URL для скрытого API Google Translate.
- **timeout_min** и **timeout_max**: Установите минимальное и максимальное время ожидания для запросов.
- **proxy**: Настройте прокси, если необходимо.
- **text_max_length**: Максимальная длина текста, который можно перевести.
- **supported_languages**: Список поддерживаемых языков.
- **user_agents**: Настроенные User-Agent для имитации различных браузеров.

## Пример использования

Для перевода текста, используйте следующий пример:

```php
<?php

namespace Anidzen\GoogleTranslateScraper;

use Anidzen\GoogleTranslateScraper\Facades\TextTranslator;
use Exception;

class GoogleTranslator
{
    private string $sourceLanguage;
    private string $targetLanguage;

    public function __construct(string $sourceLanguage = 'en', string $targetLanguage = 'ru')
    {
        $this->sourceLanguage = $sourceLanguage;
        $this->targetLanguage = $targetLanguage;
    }

    /**
     * Переводит текст с одного языка на другой.
     *
     * @param string $text Текст для перевода.
     * @return string Переведенный текст.
     * @throws Exception Если возникла ошибка при переводе.
     */
    public function translateText(string $text): string
    {
        try {
            $response = TextTranslator::translate($this->sourceLanguage, $this->targetLanguage, $text);
            
            return $response->getContent();
        } catch (Exception $e) {
            throw new Exception('Translation failed: ' . $e->getMessage());
        }
    }
}
```

В этом примере мы переводим текст "Hello, world!" с английского на русский. Библиотека возвращает ответ в формате JSON.
