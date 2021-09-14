<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Repository;

use Amasty\Finder\Api\Data\FinderInterface;
use Amasty\Finder\Api\FinderRepositoryInterface;
use Amasty\Finder\Model\FinderFactory;
use Amasty\Finder\Model\ResourceModel\Finder;
use Amasty\Finder\Model\ResourceModel\Finder\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class FinderRepository implements FinderRepositoryInterface
{
    /**
     * @var FinderFactory
     */
    private $finderFactory;
    
    /**
     * @var Finder
     */
    private $finderResource;
    
    /**
     * @var array
     */
    private $finders;
    
    /**
     * @var CollectionFactory
     */
    private $finderCollectionFactory;

    /**
     * FinderRepository constructor.
     * @param FinderFactory $finderFactory
     * @param Finder $finderResource
     * @param CollectionFactory $finderCollectionFactory
     */
    public function __construct(
        FinderFactory $finderFactory,
        Finder $finderResource,
        CollectionFactory $finderCollectionFactory
    ) {
        $this->finderFactory = $finderFactory;
        $this->finderResource = $finderResource;
        $this->finderCollectionFactory = $finderCollectionFactory;
    }

    /**
     * @param FinderInterface $finder
     * @return FinderInterface
     * @throws CouldNotSaveException
     */
    public function save(FinderInterface $finder)
    {
        try {
            $this->finderResource->save($finder);
        } catch (\Exception $e) {
            if ($finder->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save finder with ID %1. Error: %2',
                        [$finder->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new finder. Error: %1', $e->getMessage()));
        }

        return $finder;
    }

    /**
     * @param $mapId
     * @return bool
     */
    public function isDeletable($mapId)
    {
        return $this->finderResource->isDeletable($mapId);
    }

    /**
     * @param $finder
     * @param $file
     * @return array
     */
    public function importUniversal($finder, $file)
    {
        return $this->finderResource->importUniversal($finder, $file);
    }

    /**
     * @return \Amasty\Finder\Model\Finder
     */
    public function getFinderModel()
    {
        return $this->finderFactory->create();
    }

    /**
     * @param $collection
     * @param $valueId
     * @param $countEmptyDropdowns
     * @param $finderId
     * @param $isUniversal
     * @param $isUniversalLast
     * @return bool
     */
    public function addConditionToProductCollection(
        $collection,
        $valueId,
        $countEmptyDropdowns,
        $finderId,
        $isUniversal,
        $isUniversalLast
    ) {
        return $this->finderResource->addConditionToProductCollection(
            $collection,
            $valueId,
            $countEmptyDropdowns,
            $finderId,
            $isUniversal,
            $isUniversalLast
        );
    }

    /**
     * @param int $id
     * @return \Amasty\Finder\Api\Data\FinderInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        if (!isset($this->finders[$id])) {
            /** @var \Amasty\Finder\Model\Finder $finder */
            $finder = $this->finderFactory->create();
            $this->finderResource->load($finder, $id);
            $this->finders[$id] = $finder;
        }

        return $this->finders[$id];
    }

    /**
     * @param $id
     * @return array
     */
    public function getWithoutId($id)
    {
        /** @var \Amasty\Finder\Model\ResourceModel\Finder\Collection $finderCollection */
        $finderCollection = $this->finderCollectionFactory->create();
        $finderList = [];

        foreach ($finderCollection as $finder) {
            if ($finder->getId() !== $id) {
                $finderList[] = $finder;
            }
        }

        return $finderList;
    }

    /**
     * @param FinderInterface $finder
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(FinderInterface $finder)
    {
        try {
            $this->finderResource->delete($finder);
            unset($this->finders[$finder->getId()]);
        } catch (\Exception $e) {
            if ($finder->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove finder with ID %1. Error: %2',
                        [$finder->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove finder. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param $ids
     * @return bool
     */
    public function deleteByIds($ids)
    {
        $finderCollection = $this->finderCollectionFactory->create();

        $finderCollection->addFieldToFilter('finder_id', ['in' => $ids]);
        $finderCollection->walk('delete');

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $finderModel = $this->getById($id);
        $this->delete($finderModel);

        return true;
    }

    /**
     * @param $ids
     * @return Finder\Collection
     */
    public function getFindersByIds($ids)
    {
        $finderCollection = $this->finderCollectionFactory->create();

        $finderCollection->addFieldToFilter('finder_id', ['in' => $ids]);

        return $finderCollection;
    }

    /**
     * @return array
     */
    public function getList()
    {
        /** @var \Amasty\Finder\Model\ResourceModel\Finder\Collection $finderCollection */
        $finderCollection = $this->finderCollectionFactory->create();
        $finderList = [];

        foreach ($finderCollection as $finder) {
            $finderList[] = $finder;
        }

        return $finderList;
    }

    /**
     * @return array
     */
    public function updateLinks()
    {
        return $this->finderResource->updateLinks();
    }

    /**
     * @return \Amasty\Finder\Model\ResourceModel\Finder\Collection
     */
    public function getFindersOnDefaultCategory()
    {
        return $this->finderCollectionFactory->create()->addFieldToFilter('default_category', 1);
    }

    /**
     * @return \Amasty\Finder\Model\ResourceModel\Finder\Collection
     */
    public function getFindersOnSearchPage()
    {
        return $this->finderCollectionFactory->create()->addFieldToFilter('search_page', 1);
    }

    /**
     * @param int $categoryId
     * @return  \Amasty\Finder\Model\ResourceModel\Finder\Collection
     */
    public function getFindersCategory($categoryId)
    {
        return $this->finderCollectionFactory->create()->addFieldToFilter(
            'categories',
            [
                ['categories', 'like' => '%,' . $categoryId . ',%'],
                ['categories', 'like' => '%,' . \Amasty\Finder\Model\Source\Category::ALL_CATEGORIES . ',%']
            ]
        );
    }
}
