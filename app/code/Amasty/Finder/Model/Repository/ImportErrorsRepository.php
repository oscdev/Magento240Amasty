<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Repository;

use Amasty\Finder\Api\Data\ImportErrorsInterface;
use Amasty\Finder\Api\ImportErrorsRepositoryInterface;
use Amasty\Finder\Model\ImportErrorsFactory;
use Amasty\Finder\Model\ResourceModel\ImportErrors;
use Amasty\Finder\Model\ResourceModel\ImportErrors\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class ImportErrorsRepository implements ImportErrorsRepositoryInterface
{
    /**
     * @var ImportErrorsFactory
     */
    private $importErrorsFactory;
    
    /**
     * @var ImportErrors
     */
    private $importErrorsResource;
    
    /**
     * @var array
     */
    private $importErrors;
    
    /**
     * @var CollectionFactory
     */
    private $importErrorsCollectionFactory;

    /**
     * ImportErrorsRepository constructor.
     * @param ImportErrorsFactory $importErrorsFactory
     * @param ImportErrors $importErrorsResource
     * @param CollectionFactory $importErrorsCollectionFactory
     */
    public function __construct(
        ImportErrorsFactory $importErrorsFactory,
        ImportErrors $importErrorsResource,
        CollectionFactory $importErrorsCollectionFactory
    ) {
        $this->importErrorsFactory = $importErrorsFactory;
        $this->importErrorsResource = $importErrorsResource;
        $this->importErrorsCollectionFactory = $importErrorsCollectionFactory;
    }

    /**
     * @param ImportErrorsInterface $importErrors
     * @return ImportErrorsInterface
     * @throws CouldNotSaveException
     */
    public function save(ImportErrorsInterface $importErrors)
    {
        try {
            $this->importErrorsResource->save($importErrors);
        } catch (\Exception $e) {
            if ($importErrors->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save importErrors with ID %1. Error: %2',
                        [$importErrors->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new importErrors. Error: %1', $e->getMessage()));
        }

        return $importErrors;
    }

    /**
     * @param $fileId
     * @param $historyFileId
     */
    public function archiveErrorHistory($fileId, $historyFileId)
    {
        $this->importErrorsResource->archiveErrorHistory($fileId, $historyFileId);
    }

    /**
     * @param int $id
     * @return \Amasty\Finder\Api\Data\ImportErrorsInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        if (!isset($this->importErrors[$id])) {
            /** @var \Amasty\Finder\Model\ImportErrors $importErrors */
            $importErrors = $this->importErrorsFactory->create();
            $this->importErrorsResource->load($importErrors, $id);
            if (!$importErrors->getId()) {
                throw new NoSuchEntityException(__('importErrors with specified ID "%1" not found.', $id));
            }
            $this->importErrors[$id] = $importErrors;
        }

        return $this->importErrors[$id];
    }

    /**
     * @param $field
     * @param $id
     * @return \Amasty\Finder\Model\ResourceModel\ImportErrors\Collection
     */
    public function getErrorsCollection($field, $id)
    {
        return $this->importErrorsCollectionFactory->create()->addFieldToFilter($field, $id);
    }

    /**
     * @param ImportErrorsInterface $importErrors
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ImportErrorsInterface $importErrors)
    {
        try {
            $this->importErrorsResource->delete($importErrors);
            unset($this->importErrors[$importErrors->getId()]);
        } catch (\Exception $e) {
            if ($importErrors->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove importErrors with ID %1. Error: %2',
                        [$importErrors->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove importErrors. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $importErrorsModel = $this->getById($id);
        $this->delete($importErrorsModel);

        return true;
    }

    /**
     * @return array
     */
    public function getList()
    {
        /** @var \Amasty\Finder\Model\ResourceModel\ImportErrors\Collection $importErrorsCollection */
        $importErrorsCollection = $this->importErrorsCollectionFactory->create();
        $importErrorsList = [];

        foreach ($importErrorsCollection as $importErrors) {
            $importErrorsList[] = $importErrors;
        }

        return $importErrorsList;
    }
}
