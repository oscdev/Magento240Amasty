<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


declare(strict_types=1);

namespace Amasty\Finder\Model;

use Amasty\Finder\Api\Data\FinderInterface;
use Amasty\Finder\Model\ResourceModel\Finder;
use Magento\Framework\Exception\LocalizedException;

class FileValidator
{
    const ROW_COUNT_FOR_IMAGE_IMPORT = 2;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $file;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileDriver;

    public function __construct(
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\Driver\File $fileDriver
    ) {
        $this->file = $file;
        $this->fileDriver = $fileDriver;
    }

    public function validateUniversalFile(array $file)
    {
        $this->validateCsvAndNotEmpty($file);
        $line = $this->getLine($file);

        $message = '';
        if (count($line) > 1) {
            $message = __('Import File should contain only one column.');
        } elseif (strcasecmp(current($line), 'sku') !== 0) {
            $message = __('Incorrect column name.');
        }

        if ($message) {
            throw new LocalizedException($message);
        }
    }

    public function validateImageFile(array $file, FinderInterface $finder)
    {
        $this->validateCsvAndNotEmpty($file);
        $line = $this->getLine($file);

        $message = '';
        if (count($line) !== self::ROW_COUNT_FOR_IMAGE_IMPORT) {
            $message = __('Import File should contain %1 column(s).', self::ROW_COUNT_FOR_IMAGE_IMPORT);
        }

        if ($message) {
            throw new LocalizedException($message);
        }
    }

    private function validateCsvAndNotEmpty(array $file)
    {
        $fileNamePart = $this->file->getPathInfo($file['name']);
        $mimeType = function_exists('mime_content_type') ? mime_content_type($file['tmp_name']) : 'text/plain';
        $message = '';

        if ($fileNamePart['extension'] != 'csv') {
            $message = __('Incorrect file type. CSV needed');
        } elseif ($mimeType != 'text/plain') {
            $message = __('The file is empty.');
        }

        if ($message) {
            throw new LocalizedException($message);
        }
    }

    private function getLine(array $file): array
    {
        $file = $this->fileDriver->fileOpen($file['tmp_name'], 'r');

        return $this->fileDriver->fileGetCsv($file, Finder::MAX_LINE, ',', '"');
    }
}
