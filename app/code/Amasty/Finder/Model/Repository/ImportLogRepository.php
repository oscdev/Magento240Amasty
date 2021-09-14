<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Repository;

use Amasty\Finder\Api\Data\ImportLogInterface;
use Amasty\Finder\Api\ImportLogRepositoryInterface;
use Amasty\Finder\Model\ImportLogFactory;
use Amasty\Finder\Model\ResourceModel\ImportLog;
use Amasty\Finder\Model\ResourceModel\ImportLog\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class ImportLogRepository implements ImportLogRepositoryInterface
{
    /**
     * @var ImportLogFactory
     */
    private $importLogFactory;
    
    /**
     * @var ImportLog
     */
    private $importLogResource;
    
    /**
     * @var array
     */
    private $importLogs;
    
    /**
     * @var CollectionFactory
     */
    private $importLogCollectionFactory;

    /**
     * ImportLogRepository constructor.
     * @param ImportLogFactory $importLogFactory
     * @param ImportLog $importLogResource
     * @param CollectionFactory $importLogCollectionFactory
     */
    public function __construct(
        ImportLogFactory $importLogFactory,
        ImportLog $importLogResource,
        CollectionFactory $importLogCollectionFactory
    ) {
        $this->importLogFactory = $importLogFactory;
        $this->importLogResource = $importLogResource;
        $this->importLogCollectionFactory = $importLogCollectionFactory;
    }

    /**
     * @param ImportLogInterface $importLog
     * @return ImportLogInterface
     * @throws CouldNotSaveException
     */
    public function save(ImportLogInterface $importLog)
    {
        try {
            $this->importLogResource->save($importLog);
        } catch (\Exception $e) {
            if ($importLog->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save importLog with ID %1. Error: %2',
                        [$importLog->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new importLog. Error: %1', $e->getMessage()));
        }

        return $importLog;
    }

    /**
     * @param int $id
     * @return \Amasty\Finder\Api\Data\ImportLogInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        if (!isset($this->importLogs[$id])) {
            /** @var \Amasty\Finder\Model\ImportLog $importLog */
            $importLog = $this->importLogFactory->create();
            $this->importLogResource->load($importLog, $id);
            if (!$importLog->getId()) {
                throw new NoSuchEntityException(__('importLog with specified ID "%1" not found.', $id));
            }
            $this->importLogs[$id] = $importLog;
        }

        return $this->importLogs[$id];
    }

    /**
     * @param ImportLogInterface $importLog
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ImportLogInterface $importLog)
    {
        try {
            $this->importLogResource->delete($importLog);
            unset($this->importLogs[$importLog->getId()]);
        } catch (\Exception $e) {
            if ($importLog->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove importLog with ID %1. Error: %2',
                        [$importLog->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove importLog. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param $ids
     * @return bool
     */
    public function deleteByIds($ids)
    {
        $importLogCollection = $this->importLogCollectionFactory->create();

        $importLogCollection->addFieldToFilter('file_id', ['in' => $ids]);
        $importLogCollection->walk('delete');

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $importLogModel = $this->getById($id);
        $this->delete($importLogModel);

        return true;
    }

    /**
     * @param $finderId
     * @return bool
     */
    public function deleteByFinderId($finderId)
    {
        $this->importLogCollectionFactory->create()->addFieldToFilter('finder_id', $finderId)->walk('delete');

        return true;
    }

    /**
     * @return array
     */
    public function getList()
    {
        /** @var \Amasty\Finder\Model\ResourceModel\ImportLog\Collection $importLogCollection */
        $importLogCollection = $this->importLogCollectionFactory->create();
        $importLogList = [];

        foreach ($importLogCollection as $importLog) {
            $importLogList[] = $importLog;
        }

        return $importLogList;
    }

    /**
     * @return ImportLog\Collection
     */
    public function getNotLockedFiles()
    {
        $importLogCollection = $this->importLogCollectionFactory->create();
        $importLogCollection->addFieldToFilter('is_locked', 0)->orderForImport();

        return $importLogCollection;
    }

    /**
     * @param $fileName
     * @param $finderId
     * @return \Magento\Framework\DataObject[]
     */
    public function getByNameAndFinder($fileName, $finderId)
    {
        $importLogCollection = $this->importLogCollectionFactory->create();

        $importLogCollection->addFieldToFilter('file_name', $fileName)
            ->addFieldToFilter('finder_id', $finderId);
        return $importLogCollection->getItems();
    }

    /**
     * @param $file
     * @param $finderId
     * @return bool
     */
    public function addUniqueFile($file, $finderId)
    {
        $this->importLogResource->getConnection()->insertOnDuplicate(
            $this->importLogResource->getMainTable(),
            ['file_name' => $file, 'finder_id' => $finderId]
        );

        return true;
    }

    /**
     * @param $finderId
     * @return bool
     */
    public function deleteByIdWithoutReplaceFile($finderId)
    {
        $this->importLogCollectionFactory->create()
            ->addFieldToFilter('finder_id', $finderId)
            ->addFieldToFilter('file_name', ['neq' => \Amasty\Finder\Model\Import::REPLACE_CSV])
            ->walk('delete');

        return true;
    }

    /**
     * @param $finderId
     * @return bool
     */
    public function hasIssetReplaceFile($finderId)
    {
        return $this->importLogResource->hasIssetReplaceFile($finderId);
    }

    /**
     * @param $tableName
     * @return string
     */
    public function getTable($tableName)
    {
        return $this->importLogResource->getTable($tableName);
    }
}
