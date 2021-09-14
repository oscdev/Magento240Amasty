<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Model;

use Amasty\Finder\Api\Data\ImportErrorsInterface;

class ImportErrors extends \Magento\Framework\Model\AbstractModel implements ImportErrorsInterface
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Finder\Model\ResourceModel\ImportErrors::class);
    }

    /**
     * @param $fileId
     * @param $line
     * @param $message
     */
    public function error($fileId, $line, $message)
    {
        $this->setImportFileLogId($fileId)
            ->setCreatedAt(date('Y-m-d H:i:s'))
            ->setLine($line)
            ->setMessage($message)
            ->save();
    }

    /**
     * @return int
     */
    public function getErrorId()
    {
        return $this->_getData(ImportErrorsInterface::ERROR_ID);
    }

    /**
     * @param int $errorId
     * @return $this
     */
    public function setErrorId($errorId)
    {
        $this->setData(ImportErrorsInterface::ERROR_ID, $errorId);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getImportFileLogId()
    {
        return $this->_getData(ImportErrorsInterface::IMPORT_FILE_LOG_ID);
    }

    /**
     * @param int|null $importFileLogId
     * @return $this
     */
    public function setImportFileLogId($importFileLogId)
    {
        $this->setData(ImportErrorsInterface::IMPORT_FILE_LOG_ID, $importFileLogId);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getImportFileLogHistoryId()
    {
        return $this->_getData(ImportErrorsInterface::IMPORT_FILE_LOG_HISTORY_ID);
    }

    /**
     * @param int|null $importFileLogHistoryId
     * @return $this
     */
    public function setImportFileLogHistoryId($importFileLogHistoryId)
    {
        $this->setData(ImportErrorsInterface::IMPORT_FILE_LOG_HISTORY_ID, $importFileLogHistoryId);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_getData(ImportErrorsInterface::CREATED_AT);
    }

    /**
     * @param null|string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(ImportErrorsInterface::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->_getData(ImportErrorsInterface::LINE);
    }

    /**
     * @param int $line
     * @return $this
     */
    public function setLine($line)
    {
        $this->setData(ImportErrorsInterface::LINE, $line);

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_getData(ImportErrorsInterface::MESSAGE);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->setData(ImportErrorsInterface::MESSAGE, $message);

        return $this;
    }
}
