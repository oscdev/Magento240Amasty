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

use Amasty\Finder\Api\Data\ImportLogInterface;
use Magento\Framework\Exception\CouldNotSaveException;

class ImportLog extends \Amasty\Finder\Model\Import\ImportLogAbstract implements ImportLogInterface
{
    const STATE_UPLOADED = 0;
    const STATE_PROCESSING = 1;

    const FILE_STATE = 'processing';

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Finder\Model\ResourceModel\ImportLog::class);
    }

    protected function _beforeSave()
    {
        $this->setUpdatedAt(date('Y-m-d H:i:s'));
        return parent::_beforeSave();
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getState()
    {
        if ($this->getStatus() == self::STATE_UPLOADED) {
            $state = __('Uploaded');
        } else {
            $state = __('Processing');
            $state .= " " . $this->getCountProcessingLines() . " " . __('lines of') . " " .
                $this->getCountLines() . ".";
            if ($this->getCountErrors() > 0) {
                $state .= " " . $this->getCountErrors() . " " . __('errors') . ".";
            }
        }
        return $state;
    }

    /**
     * @return bool
     */
    public function isProcessing()
    {
        return $this->getStatus() == self::STATE_PROCESSING;
    }

    /**
     * @return $this
     */
    public function afterDelete()
    {
        $filePath = $this->getFilePath();
        if (is_file($filePath)) {
            unlink($filePath);
        }
        return parent::afterDelete();
    }

    /**
     * @return string
     */
    public function getFileState()
    {
        return \Amasty\Finder\Helper\Import::FILE_STATE_PROCESSING;
    }

    /**
     * @return string
     */
    public function getFieldInErrorLog()
    {
        return 'import_file_log_id';
    }

    /**
     * @return $this
     */
    public function archive()
    {
        $data = $this->getData();
        $data['file_id'] = null;
        $fileLogHistory = $this->historyRepository->saveData($data);

        $this->setFileLogHistoryId($fileLogHistory->getId());
        $this->errorsRepository->archiveErrorHistory($this->getId(), $fileLogHistory->getId());

        $filePath = $this->getFilePath();
        $newFilePath = $this->helper->getImportArchiveDir() . $fileLogHistory->getId() . ".csv";
        if (is_file($filePath)) {
            rename($filePath, $newFilePath);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->helper->getFtpImportDir() . $this->getFinderId() . '/' . $this->getFileName();
    }

    /**
     * @return float|int
     */
    public function getProgress()
    {
        return ($this->getCountLines()) ? floor($this->getCountProcessingLines() / $this->getCountLines() * 100) : 100;
    }

    /**
     * @return $this
     */
    public function error()
    {
        $this->setCountErrors($this->getCountErrors() + 1);
        return $this;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getMode()
    {
        return $this->getFileName() == \Amasty\Finder\Model\Import::REPLACE_CSV
            ? __('Replace Products')
            : __('Add Products');
    }

    /**
     * @return int
     */
    public function getFileId()
    {
        return $this->_getData(ImportLogInterface::FILE_ID);
    }

    /**
     * @param int $fileId
     * @return $this
     */
    public function setFileId($fileId)
    {
        $this->setData(ImportLogInterface::FILE_ID, $fileId);

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->_getData(ImportLogInterface::FILE_NAME);
    }

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->setData(ImportLogInterface::FILE_NAME, $fileName);

        return $this;
    }

    /**
     * @return int
     */
    public function getFinderId()
    {
        return $this->_getData(ImportLogInterface::FINDER_ID);
    }

    /**
     * @param int $finderId
     * @return $this
     */
    public function setFinderId($finderId)
    {
        $this->setData(ImportLogInterface::FINDER_ID, $finderId);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStartedAt()
    {
        return $this->_getData(ImportLogInterface::STARTED_AT);
    }

    /**
     * @param null|string $startedAt
     * @return $this
     */
    public function setStartedAt($startedAt)
    {
        $this->setData(ImportLogInterface::STARTED_AT, $startedAt);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_getData(ImportLogInterface::UPDATED_AT);
    }

    /**
     * @param null|string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(ImportLogInterface::UPDATED_AT, $updatedAt);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEndedAt()
    {
        return $this->_getData(ImportLogInterface::ENDED_AT);
    }

    /**
     * @param null|string $endedAt
     * @return $this
     */
    public function setEndedAt($endedAt)
    {
        $this->setData(ImportLogInterface::ENDED_AT, $endedAt);

        return $this;
    }

    /**
     * @return int
     */
    public function getCountLines()
    {
        return $this->_getData(ImportLogInterface::COUNT_LINES);
    }

    /**
     * @param int $countLines
     * @return $this
     */
    public function setCountLines($countLines)
    {
        $this->setData(ImportLogInterface::COUNT_LINES, $countLines);

        return $this;
    }

    /**
     * @return int
     */
    public function getCountProcessingLines()
    {
        return $this->_getData(ImportLogInterface::COUNT_PROCESSING_LINES);
    }

    /**
     * @param int $countProcessingLines
     * @return $this
     */
    public function setCountProcessingLines($countProcessingLines)
    {
        $this->setData(ImportLogInterface::COUNT_PROCESSING_LINES, $countProcessingLines);

        return $this;
    }

    /**
     * @return int
     */
    public function getLastStartProcessingLine()
    {
        return $this->_getData(ImportLogInterface::LAST_START_PROCESSING_LINE);
    }

    /**
     * @param int $lastStartProcessingLine
     * @return $this
     */
    public function setLastStartProcessingLine($lastStartProcessingLine)
    {
        $this->setData(ImportLogInterface::LAST_START_PROCESSING_LINE, $lastStartProcessingLine);

        return $this;
    }

    /**
     * @return int
     */
    public function getCountProcessingRows()
    {
        return $this->_getData(ImportLogInterface::COUNT_PROCESSING_ROWS);
    }

    /**
     * @param int $countProcessingRows
     * @return $this
     */
    public function setCountProcessingRows($countProcessingRows)
    {
        $this->setData(ImportLogInterface::COUNT_PROCESSING_ROWS, $countProcessingRows);

        return $this;
    }

    /**
     * @return int
     */
    public function getCountErrors()
    {
        return $this->_getData(ImportLogInterface::COUNT_ERRORS);
    }

    /**
     * @param int $countErrors
     * @return $this
     */
    public function setCountErrors($countErrors)
    {
        $this->setData(ImportLogInterface::COUNT_ERRORS, $countErrors);

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->_getData(ImportLogInterface::STATUS);
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData(ImportLogInterface::STATUS, $status);

        return $this;
    }

    /**
     * @return int
     */
    public function getIsLocked()
    {
        return $this->_getData(ImportLogInterface::IS_LOCKED);
    }

    /**
     * @param int $isLocked
     * @return $this
     */
    public function setIsLocked($isLocked)
    {
        $this->setData(ImportLogInterface::IS_LOCKED, $isLocked);

        return $this;
    }
}
