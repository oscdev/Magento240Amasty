<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Finder\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class Import extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SORT_STRING_ASC = 0;
    const SORT_STRING_DESC = 1;
    const SORT_NUM_ASC = 2;
    const SORT_NUM_DESC = 3;

    const FTP_IMPORT_DIR = '/amasty/finder/ftp_import/';

    const FILE_STATE_PROCESSING = 'processing';
    const FILE_STATE_ARCHIVE = 'archive';

    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $file;

    /**
     * Import constructor.
     * @param Context $context
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file
    ) {
        parent::__construct($context);
        $this->rootDirectory = $directoryList->getPath(DirectoryList::MEDIA);
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFtpImportDir()
    {
        return $this->rootDirectory . self::FTP_IMPORT_DIR;
    }

    /**
     * @return string
     */
    public function getImportArchiveDir()
    {
        $dir = $this->getFtpImportDir() . 'archive/';
        $this->file->checkAndCreateFolder($dir);

        return $dir;
    }

    /**
     * @return int
     */
    public function getArchiveLifetime()
    {
        return $this->scopeConfig->getValue('amfinder/import/archive_lifetime');
    }
}
