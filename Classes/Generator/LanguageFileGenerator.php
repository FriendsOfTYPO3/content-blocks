<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\ContentBlocks\Generator;

use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\AutomaticLanguageKeysRegistry;
use TYPO3\CMS\ContentBlocks\Registry\AutomaticLanguageSource;
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistry;
use TYPO3\CMS\Core\Localization\TranslationDomainMapper;

/**
 * @internal Not part of TYPO3's public API.
 */
class LanguageFileGenerator
{
    public function __construct(
        protected AutomaticLanguageKeysRegistry $automaticLanguageKeysRegistry,
        protected readonly LanguageFileRegistry $languageFileRegistry,
        protected readonly TranslationDomainMapper $translationDomainMapper,
    ) {}

    public function generate(LoadedContentBlock $contentBlock, ?string $date = null): string
    {
        $items = [];
        $automaticKeys = [];
        $sources = $this->automaticLanguageKeysRegistry->getByContentBlock($contentBlock);
        foreach ($sources as $source) {
            // translations defined in xlf file have precedence.
            if ($this->languageFileRegistry->isset($contentBlock->getName(), $source->key)) {
                $value = $this->languageFileRegistry->get($contentBlock->getName(), $source->key);
                $source = new AutomaticLanguageSource($source->key, $value);
            }
            if ($source->value === '') {
                continue;
            }
            if (str_contains($source->value, ':')) {
                [$possibleDomain] = explode(':', $source->value, 2);
                if (
                    $this->translationDomainMapper->isValidDomainName($possibleDomain)
                    && $this->translationDomainMapper->mapDomainToFileName($possibleDomain) !== $possibleDomain
                ) {
                    continue;
                }
            }
            $automaticKeys[] = $source->key;
            $items[] = $this->generateTransUnitFromSource($source);
        }
        $allKeys = $this->languageFileRegistry->getAllRegisteredKeys($contentBlock->getName());
        $customKeys = array_diff($allKeys, $automaticKeys);
        $customSources = $this->createSourcesFromKeys($contentBlock, $customKeys);
        foreach ($customSources as $source) {
            $items[] = $this->generateTransUnitFromSource($source);
        }
        $itemsConcatenated = implode("\n", $items);
        if ($date === null) {
            $utc = new \DateTimeZone('UTC');
            $date = (new \DateTime())->setTimezone($utc)->format('c');
        }
        $result = $this->wrap($itemsConcatenated, $date, $contentBlock->getVendor(), $contentBlock->getPackage());
        $result .= "\n";
        return $result;
    }

    /**
     * @param string[] $keys
     * @return AutomaticLanguageSource[]
     */
    protected function createSourcesFromKeys(LoadedContentBlock $contentBlock, array $keys): array
    {
        $sources = [];
        foreach ($keys as $key) {
            $value = $this->languageFileRegistry->get($contentBlock->getName(), $key);
            $source = new AutomaticLanguageSource($key, $value);
            $sources[] = $source;
        }
        return $sources;
    }

    protected function generateTransUnitFromSource(AutomaticLanguageSource $automaticLanguageSource): string
    {
        $key = $automaticLanguageSource->key;
        $value = htmlspecialchars($automaticLanguageSource->value);
        $sourceContent = <<<HEREDOC
      <trans-unit id="$key">
        <source>$value</source>
      </trans-unit>
HEREDOC;
        return $sourceContent;
    }

    protected function wrap(string $content, string $date, string $vendor, string $name): string
    {
        $xliffContent = <<<HEREDOC
<?xml version="1.0"?>
<xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
  <file datatype="plaintext" original="labels.xlf" source-language="en" date="$date" product-name="$vendor/$name">
    <header/>
    <body>
$content
    </body>
  </file>
</xliff>
HEREDOC;
        return $xliffContent;
    }

    public function setAutomaticLanguageKeysRegistry(AutomaticLanguageKeysRegistry $automaticLanguageKeysRegistry): void
    {
        $this->automaticLanguageKeysRegistry = $automaticLanguageKeysRegistry;
    }
}
