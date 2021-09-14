<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Repository;

use Amasty\Finder\Api\Data\UniversalInterface;
use Amasty\Finder\Api\UniversalRepositoryInterface;
use Amasty\Finder\Model\UniversalFactory;
use Amasty\Finder\Model\ResourceModel\Universal;
use Amasty\Finder\Model\ResourceModel\Universal\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class UniversalRepository implements UniversalRepositoryInterface
{
    /**
     * @var UniversalFactory
     */
    private $universalFactory;
    
    /**
     * @var Universal
     */
    private $universalResource;
    
    /**
     * @var array
     */
    private $universals;
    
    /**
     * @var CollectionFactory
     */
    private $universalCollectionFactory;

    /**
     * UniversalRepository constructor.
     * @param UniversalFactory $universalFactory
     * @param Universal $universalResource
     * @param CollectionFactory $universalCollectionFactory
     */
    public function __construct(
        UniversalFactory $universalFactory,
        Universal $universalResource,
        CollectionFactory $universalCollectionFactory
    ) {
        $this->universalFactory = $universalFactory;
        $this->universalResource = $universalResource;
        $this->universalCollectionFactory = $universalCollectionFactory;
    }

    /**
     * @param UniversalInterface $universal
     * @return UniversalInterface
     * @throws CouldNotSaveException
     */
    public function save(UniversalInterface $universal)
    {
        try {
            $this->universalResource->save($universal);
        } catch (\Exception $e) {
            if ($universal->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save universal with ID %1. Error: %2',
                        [$universal->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new universal. Error: %1', $e->getMessage()));
        }

        return $universal;
    }

    /**
     * @param int $id
     * @return \Amasty\Finder\Api\Data\UniversalInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        if (!isset($this->universals[$id])) {
            /** @var \Amasty\Finder\Model\Universal $universal */
            $universal = $this->universalFactory->create();
            $this->universalResource->load($universal, $id);
            if (!$universal->getId()) {
                throw new NoSuchEntityException(__('universal with specified ID "%1" not found.', $id));
            }
            $this->universals[$id] = $universal;
        }

        return $this->universals[$id];
    }

    /**
     * @param UniversalInterface $universal
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(UniversalInterface $universal)
    {
        try {
            $this->universalResource->delete($universal);
            unset($this->universals[$universal->getId()]);
        } catch (\Exception $e) {
            if ($universal->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove universal with ID %1. Error: %2',
                        [$universal->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove universal. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $universalModel = $this->getById($id);
        $this->delete($universalModel);

        return true;
    }

    /**
     * @param $ids
     * @return bool
     */
    public function deleteByIds($ids)
    {
        $universalCollection = $this->universalCollectionFactory->create();

        $universalCollection->addFieldToFilter('universal_id', ['in' => $ids]);
        $universalCollection->walk('delete');

        return true;
    }

    /**
     * @return array
     */
    public function getList()
    {
        /** @var \Amasty\Finder\Model\ResourceModel\Universal\Collection $universalCollection */
        $universalCollection = $this->universalCollectionFactory->create();
        $universalList = [];

        foreach ($universalCollection as $universal) {
            $universalList[] = $universal;
        }

        return $universalList;
    }
}
