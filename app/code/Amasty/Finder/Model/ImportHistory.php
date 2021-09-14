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

use Amasty\Finder\Api\Data\ImportHistoryInterface;

class ImportHistory extends \Amasty\Finder\Model\Import\ImportLogAbstract implements ImportHistoryInterface
{
    /**
     * ImportHistory constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Amasty\Finder\Helper\Import $helper
     * @param \Amasty\Finder\Api\ImportErrorsRepositoryInterface $errorsRepository
     * @param \Amasty\Finder\Api\ImportHistoryRepositoryInterface $historyRepository
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Finder\Helper\Import $helper,
        \Amasty\Finder\Api\ImportErrorsRepositoryInterface $errorsRepository,
        \Amasty\Finder\Api\ImportHistoryRepositoryInterface $historyRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $errorsRepository,
            $historyRepository,
            $helper,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\Finder\Model\ResourceModel\ImportHistory::class);
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getFileState()
    {
        return \Amasty\Finder\Helper\Import::FILE_STATE_ARCHIVE;
    }

    /**
     * @return string
     */
    public function getFieldInErrorLog()
    {
        return 'import_file_log_history_id';
    }

    /**
     * @return $this
     */
    public function afterDelete()
    {
        $file = $this->helper->getImportArchiveDir() . $this->getId() . '.csv';

        if (is_file($file)) {
            unlink($file);
        }
        return parent::afterDelete();
    }

    /**
     * @return int
     */
    public function getFileId()
    {
        return $this->_getData(ImportHistoryInterface::FILE_ID);
    }

    /**
     * @param int $fileId
     * @return $this
     */
    public function setFileId($fileId)
    {
        $this->setData(ImportHistoryInterface::FILE_ID, $fileId);

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->_getData(ImportHistoryInterface::FILE_NAME);
    }

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->setData(ImportHistoryInterface::FILE_NAME, $fileName);

        return $this;
    }

    /**
     * @return int
     */
    public function getFinderId()
    {
        return $this->_getData(ImportHistoryInterface::FINDER_ID);
    }

    /**
     * @param int $finderId
     * @return $this
     */
    public function setFinderId($finderId)
    {
        $this->setData(ImportHistoryInterface::FINDER_ID, $finderId);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStartedAt()
    {
        return $this->_getData(ImportHistoryInterface::STARTED_AT);
    }

    /**
     * @param null|string $startedAt
     * @return $this
     */
    public function setStartedAt($startedAt)
    {
        $this->setData(ImportHistoryInterface::STARTED_AT, $startedAt);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_getData(ImportHistoryInterface::UPDATED_AT);
    }

    /**
     * @param null|string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(ImportHistoryInterface::UPDATED_AT, $updatedAt);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEndedAt()
    {
        return $this->_getData(ImportHistoryInterface::ENDED_AT);
    }

    /**
     * @param null|string $endedAt
     * @return $this
     */
    public function setEndedAt($endedAt)
    {
        $this->setData(ImportHistoryInterface::ENDED_AT, $endedAt);

        return $this;
    }

    /**
     * @return int
     */
    public function getCountLines()
    {
        return $this->_getData(ImportHistoryInterface::COUNT_LINES);
    }

    /**
     * @param int $countLines
     * @return $this
     */
    public function setCountLines($countLines)
    {
        $this->setData(ImportHistoryInterface::COUNT_LINES, $countLines);

        return $this;
    }

    /**
     * @return int
     */
    public function getCountProcessingLines()
    {
        return $this->_getData(ImportHistoryInterface::COUNT_PROCESSING_LINES);
    }

    /**
     * @param int $countProcessingLines
     * @return $this
     */
    public function setCountProcessingLines($countProcessingLines)
    {
        $this->setData(ImportHistoryInterface::COUNT_PROCESSING_LINES, $countProcessingLines);

        return $this;
    }

    /**
     * @return int
     */
    public function getCountProcessingRows()
    {
        return $this->_getData(ImportHistoryInterface::COUNT_PROCESSING_ROWS);
    }

    /**
     * @param int $countProcessingRows
     * @return $this
     */
    public function setCountProcessingRows($countProcessingRows)
    {
        $this->setData(ImportHistoryInterface::COUNT_PROCESSING_ROWS, $countProcessingRows);

        return $this;
    }

    /**
     * @return int
     */
    public function getCountErrors()
    {
        return $this->_getData(ImportHistoryInterface::COUNT_ERRORS);
    }

    /**
     * @param int $countErrors
     * @return $this
     */
    public function setCountErrors($countErrors)
    {
        $this->setData(ImportHistoryInterface::COUNT_ERRORS, $countErrors);

        return $this;
    }
}
