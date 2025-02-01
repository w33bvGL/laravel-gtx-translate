# Google-Translate-Scraper

- [🇷🇺 README на русском](README_RU.md)
- [🇬🇪 Հայերեն README](README_HY.md)

**Google-Translate-Scraper** is a PHP Laravel library designed to scrape Google Translate to translate text between different languages. It provides a simple interface for performing translations.

This package allows you to translate text from one language to another by scraping Google Translate. It handles tasks such as setting request timeouts, proxies, and supporting multiple languages. It is a solution for developers who want an offline translation tool without having to pay for API keys or rely on third-party services.

## Features
- **Google Translate Scraping**: Translate text by scraping the hidden Google Translate API.
- **Custom Timeouts**: Set minimum and maximum timeouts to delay requests.
- **Proxy Support**: Use proxies for scraping to avoid rate limiting and blocking.
- **Supported Languages**: Easily translate between multiple languages ​​supported by Google Translate.
- **Customizable User Agents**: Emulate different browsers to avoid detection.

## Installation
You can install the **google-translate-scraper** library using Composer. To do this, run the following command in your terminal:

```bash
composer require anidzen/google-translate-scraper
```

## Configuration
After installing the library, you need to configure its parameters in the `google-translate-scraper.php` configuration file. In the file, you can specify the following parameters:

- **base_url**: URL for the main resource.
- **hidden_api_base_url**: URL for the hidden Google Translate API.
- **timeout_min** and **timeout_max**: Set the minimum and maximum timeout for requests.
- **proxy**: Set up a proxy if needed.
- **text_max_length**: Maximum length of text that can be translated.
- **supported_languages**: List of supported languages.
- **user_agents**: Customized User-Agent to simulate different browsers.

## Usage example

To translate text, use the following example:

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
* Translates text from one language to another.
*
* @param string $text Text to translate.
* @return string Translated text.
* @throws Exception If an error occurred during translation.
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

In this example, we translate the text "Hello, world!" from English to Russian. The library returns a response in JSON format.
