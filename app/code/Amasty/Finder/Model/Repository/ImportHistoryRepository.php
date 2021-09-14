<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Repository;

use Amasty\Finder\Api\Data\ImportHistoryInterface;
use Amasty\Finder\Api\ImportHistoryRepositoryInterface;
use Amasty\Finder\Model\ImportHistoryFactory;
use Amasty\Finder\Model\ResourceModel\ImportHistory;
use Amasty\Finder\Model\ResourceModel\ImportHistory\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class ImportHistoryRepository implements ImportHistoryRepositoryInterface
{
    /**
     * @var ImportHistoryFactory
     */
    private $importHistoryFactory;
    
    /**
     * @var ImportHistory
     */
    private $importHistoryResource;
    
    /**
     * @var array
     */
    private $importHistorys;
    
    /**
     * @var CollectionFactory
     */
    private $importHistoryCollectionFactory;

    /**
     * ImportHistoryRepository constructor.
     * @param ImportHistoryFactory $importHistoryFactory
     * @param ImportHistory $importHistoryResource
     * @param CollectionFactory $importHistoryCollectionFactory
     */
    public function __construct(
        ImportHistoryFactory $importHistoryFactory,
        ImportHistory $importHistoryResource,
        CollectionFactory $importHistoryCollectionFactory
    ) {
        $this->importHistoryFactory = $importHistoryFactory;
        $this->importHistoryResource = $importHistoryResource;
        $this->importHistoryCollectionFactory = $importHistoryCollectionFactory;
    }

    /**
     * @param ImportHistoryInterface $importHistory
     * @return ImportHistoryInterface
     * @throws CouldNotSaveException
     */
    public function save(ImportHistoryInterface $importHistory)
    {
        try {
            $this->importHistoryResource->save($importHistory);
        } catch (\Exception $e) {
            if ($importHistory->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save importHistory with ID %1. Error: %2',
                        [$importHistory->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new importHistory. Error: %1', $e->getMessage()));
        }

        return $importHistory;
    }

    /**
     * @param $data
     * @return \Amasty\Finder\Model\ImportHistory
     */
    public function saveData($data)
    {
        $historyModel = $this->importHistoryFactory->create();
        $historyModel->setData($data);
        $this->save($historyModel);
        return $historyModel;
    }

    /**
     * @param int $id
     * @return \Amasty\Finder\Api\Data\ImportHistoryInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        if (!isset($this->importHistorys[$id])) {
            /** @var \Amasty\Finder\Model\ImportHistory $importHistory */
            $importHistory = $this->importHistoryFactory->create();
            $this->importHistoryResource->load($importHistory, $id);
            if (!$importHistory->getId()) {
                throw new NoSuchEntityException(__('importHistory with specified ID "%1" not found.', $id));
            }
            $this->importHistorys[$id] = $importHistory;
        }

        return $this->importHistorys[$id];
    }

    /**
     * @param ImportHistoryInterface $importHistory
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ImportHistoryInterface $importHistory)
    {
        try {
            $this->importHistoryResource->delete($importHistory);
            unset($this->importHistorys[$importHistory->getId()]);
        } catch (\Exception $e) {
            if ($importHistory->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove importHistory with ID %1. Error: %2',
                        [$importHistory->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove importHistory. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param $finderId
     * @return bool
     */
    public function deleteByFinderId($finderId)
    {
        $this->importHistoryCollectionFactory->create()->addFieldToFilter('finder_id', $finderId)->walk('delete');

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $importHistoryModel = $this->getById($id);
        $this->delete($importHistoryModel);

        return true;
    }

    /**
     * @param $ids
     * @return bool
     */
    public function deleteByIds($ids)
    {
        $importHistoryCollection = $this->importHistoryCollectionFactory->create();

        $importHistoryCollection->addFieldToFilter('file_id', ['in' => $ids]);
        $importHistoryCollection->walk('delete');

        return true;
    }

    /**
     * @param $date
     * @return bool
     */
    public function clearLogHistory($date)
    {
        $this->importHistoryCollectionFactory->create()
            ->addFieldToFilter('ended_at', ["lteq" => $date])
            ->walk('delete');

        return true;
    }

    /**
     * @return array
     */
    public function getList()
    {
        /** @var \Amasty\Finder\Model\ResourceModel\ImportHistory\Collection $importHistoryCollection */
        $importHistoryCollection = $this->importHistoryCollectionFactory->create();
        $importHistoryList = [];

        foreach ($importHistoryCollection as $importHistory) {
            $importHistoryList[] = $importHistory;
        }

        return $importHistoryList;
    }
}
