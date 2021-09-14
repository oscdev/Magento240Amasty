<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Repository;

use Amasty\Finder\Api\Data\DropdownInterface;
use Amasty\Finder\Api\DropdownRepositoryInterface;
use Amasty\Finder\Model\DropdownFactory;
use Amasty\Finder\Model\ResourceModel\Dropdown;
use Amasty\Finder\Model\ResourceModel\Dropdown\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class DropdownRepository implements DropdownRepositoryInterface
{
    /**
     * @var DropdownFactory
     */
    private $dropdownFactory;
    
    /**
     * @var Dropdown
     */
    private $dropdownResource;
    
    /**
     * @var array
     */
    private $dropdowns;
    
    /**
     * @var CollectionFactory
     */
    private $dropdownCollectionFactory;

    /**
     * DropdownRepository constructor.
     * @param DropdownFactory $dropdownFactory
     * @param Dropdown $dropdownResource
     * @param CollectionFactory $dropdownCollectionFactory
     */
    public function __construct(
        DropdownFactory $dropdownFactory,
        Dropdown $dropdownResource,
        CollectionFactory $dropdownCollectionFactory
    ) {
        $this->dropdownFactory = $dropdownFactory;
        $this->dropdownResource = $dropdownResource;
        $this->dropdownCollectionFactory = $dropdownCollectionFactory;
    }

    /**
     * @param DropdownInterface $dropdown
     * @return DropdownInterface
     * @throws CouldNotSaveException
     */
    public function save(DropdownInterface $dropdown)
    {
        try {
            $this->dropdownResource->save($dropdown);
        } catch (\Exception $e) {
            if ($dropdown->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save dropdown with ID %1. Error: %2',
                        [$dropdown->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new dropdown. Error: %1', $e->getMessage()));
        }

        return $dropdown;
    }

    /**
     * @param int $id
     * @return \Amasty\Finder\Api\Data\DropdownInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        if (!isset($this->dropdowns[$id])) {
            /** @var \Amasty\Finder\Model\Dropdown $dropdown */
            $dropdown = $this->dropdownFactory->create();
            $this->dropdownResource->load($dropdown, $id);
            if (!$dropdown->getId()) {
                throw new NoSuchEntityException(__('dropdown with specified ID "%1" not found.', $id));
            }
            $this->dropdowns[$id] = $dropdown;
        }

        return $this->dropdowns[$id];
    }

    /**
     * @return \Amasty\Finder\Model\Dropdown
     */
    public function getDropdownModel()
    {
        return $this->dropdownFactory->create();
    }

    /**
     * @param $finderId
     * @return array
     */
    public function getByFinderId($finderId)
    {
        $dropdownCollection = $this->dropdownCollectionFactory->create();
        $dropdownList = [];

        foreach ($dropdownCollection as $dropdown) {
            if ($dropdown->getFinderId() == $finderId) {
                $dropdownList[] = $dropdown;
            }
        }

        return $dropdownList;
    }

    /**
     * @param DropdownInterface $dropdown
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(DropdownInterface $dropdown)
    {
        try {
            $this->dropdownResource->delete($dropdown);
            unset($this->dropdowns[$dropdown->getId()]);
        } catch (\Exception $e) {
            if ($dropdown->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove dropdown with ID %1. Error: %2',
                        [$dropdown->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove dropdown. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $dropdownModel = $this->getById($id);
        $this->delete($dropdownModel);

        return true;
    }

    /**
     * @return array
     */
    public function getList()
    {
        /** @var \Amasty\Finder\Model\ResourceModel\Dropdown\Collection $dropdownCollection */
        $dropdownCollection = $this->dropdownCollectionFactory->create();
        $dropdownList = [];

        foreach ($dropdownCollection as $dropdown) {
            $dropdownList[] = $dropdown;
        }

        return $dropdownList;
    }
}
