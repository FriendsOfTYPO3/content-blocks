<?php

declare(strict_types=1);

namespace TYPO3\CMS\ContentBlocks\JsonSchemaValidation;

interface ErrorFormatterInterface
{
    /**
     * @param array<string, string> $errors
     */
    public function format(array $errors): string;
}
