<?php

declare(strict_types=1);

namespace TYPO3\CMS\ContentBlocks\JsonSchemaValidation;

class JsonErrorFormatter implements ErrorFormatterInterface
{
    public function format(array $errors): string
    {
        $formattedErrors = json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return $formattedErrors;
    }
}
