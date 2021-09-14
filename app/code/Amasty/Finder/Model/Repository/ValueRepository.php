<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Repository;

use Amasty\Finder\Api\Data\ValueInterface;
use Amasty\Finder\Api\FinderRepositoryInterface;
use Amasty\Finder\Api\ValueRepositoryInterface;
use Amasty\Finder\Model\ValueFactory;
use Amasty\Finder\Model\ResourceModel\Value;
use Amasty\Finder\Model\ResourceModel\Value\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;

class ValueRepository implements ValueRepositoryInterface
{
    /**
     * @var ValueFactory
     */
    private $valueFactory;
    
    /**
     * @var Value
     */
    private $valueResource;
    
    /**
     * @var array
     */
    private $values;
    
    /**
     * @var CollectionFactory
     */
    private $valueCollectionFactory;

    /**
     * @var string
     */
    private $rootDirectory;

    /**
     * @var FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * @var LoggerInterface
     */
    private $logInterface;

    public function __construct(
        ValueFactory $valueFactory,
        Value $valueResource,
        CollectionFactory $valueCollectionFactory,
        DirectoryList $directoryList,
        FinderRepositoryInterface $finderRepository,
        LoggerInterface $logInterface
    ) {
        $this->valueFactory = $valueFactory;
        $this->valueResource = $valueResource;
        $this->valueCollectionFactory = $valueCollectionFactory;
        $this->rootDirectory = $directoryList->getPath(DirectoryList::MEDIA);
        $this->finderRepository = $finderRepository;
        $this->logInterface = $logInterface;
    }

    /**
     * @param ValueInterface $value
     * @return ValueInterface
     * @throws CouldNotSaveException
     */
    public function save(ValueInterface $value)
    {
        try {
            $this->valueResource->save($value);
        } catch (\Exception $e) {
            if ($value->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save value with ID %1. Error: %2',
                        [$value->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new value. Error: %1', $e->getMessage()));
        }

        return $value;
    }

    /**
     * @param $parentId
     * @param $dropdownId
     * @param $name
     * @return bool
     */
    public function saveValue($parentId, $dropdownId, $name)
    {
        $connection = $this->valueResource->getConnection();
        $connection->insertOnDuplicate($this->valueResource->getTable('amasty_finder_value'), [
            'parent_id' => $parentId,
            'dropdown_id' => $dropdownId,
            'name' => $name
        ]);

        return true;
    }

    /**
     * @param $finder
     * @param $file
     * @return array
     */
    public function importImages($finder, $file)
    {
        return $this->valueResource->importImages($finder, $file);
    }

    /**
     * @param array $data
     * @return int
     */
    public function saveNewFinder(array $data)
    {
        return $this->valueResource->saveNewFinder($data);
    }

    /**
     * @return \Amasty\Finder\Model\Value
     */
    public function getValueModel()
    {
        return $this->valueFactory->create();
    }

    /**
     * @param int $id
     * @return \Amasty\Finder\Api\Data\ValueInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        if (!isset($this->values[$id])) {
            /** @var \Amasty\Finder\Model\Value $value */
            $value = $this->valueFactory->create();
            $this->valueResource->load($value, $id);
            if (!$value->getId()) {
                throw new NoSuchEntityException(__('value with specified ID "%1" not found.', $id));
            }
            $this->values[$id] = $value;
        }

        return $this->values[$id];
    }

    /**
     * @param $id
     * @return \Amasty\Finder\Model\Value
     */
    public function getByParentId($id)
    {
        $value = $this->valueFactory->create();
        $this->valueResource->load($value, $id, 'parent_id');
        return $value;
    }

    /**
     * @param $parentId
     * @param $dropdownId
     * @return Value\Collection
     */
    public function getByParentAndDropdownIds($parentId, $dropdownId)
    {
        $valueCollection = $this->valueCollectionFactory->create();
        $valueCollection->addFieldToFilter('dropdown_id', ['in' => $dropdownId])
            ->addFieldToFilter('parent_id', $parentId);

        return $valueCollection;
    }

    /**
     * @param $newId
     * @param $finderId
     * @return string
     */
    public function getSkuById($newId, $finderId)
    {
        return $this->valueResource->getSkuById($newId, $finderId);
    }

    /**
     * @param ValueInterface $value
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ValueInterface $value)
    {
        try {
            $this->valueResource->deleteImageFromDir($value->getImage());
            $this->valueResource->delete($value);
            unset($this->values[$value->getId()]);
        } catch (\Exception $e) {
            if ($value->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove value with ID %1. Error: %2',
                        [$value->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove value. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $id
     * @param int|\Amasty\Finder\Model\Finder $finder
     * @return bool
     */
    public function deleteById($id, $finder)
    {
        $finder = is_object($finder) ? $finder : $this->finderRepository->getById($finder);
        $newId = $finder->newSetterId($id);
        $finder->deleteMapRow($id);

        $currentId = $newId;
        while (($currentId) && ($finder->isDeletable($currentId))) {
            $value = $this->getById($currentId);
            $currentId = $value->getParentId();
            $this->delete($value);
        }

        return true;
    }

    /**
     * @param array $ids
     * @param \Amasty\Finder\Model\Finder $finder
     * @return bool
     */
    public function deleteByIds($ids, $finder)
    {
        foreach ($ids as $id) {
            $this->deleteById((int) $id, $finder);
        }

        return true;
    }

    /**
     * @param $finder
     * @return bool
     */
    public function deleteOldData($finder)
    {
        $ids = [];
        foreach ($finder->getDropdowns() as $dropdown) {
            $ids[] = $dropdown->getId();
        }

        $valueCollection = $this->valueCollectionFactory->create();

        $valueCollection->addFieldToFilter('dropdown_id', ['in' => $ids]);
        foreach ($valueCollection as $value) {
            $this->valueResource->deleteImageFromDir($value->getImage());
        }
        $valueCollection->walk('delete');

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteOnlyValue($id)
    {
        try {
            $this->delete($this->getById($id));
        } catch (\Exception $exception) {
            $this->logInterface->error($exception->getMessage());
        }

        return true;
    }

    /**
     * @return array
     */
    public function getList()
    {
        /** @var \Amasty\Finder\Model\ResourceModel\Value\Collection $valueCollection */
        $valueCollection = $this->valueCollectionFactory->create();
        $valueList = [];

        foreach ($valueCollection as $value) {
            $valueList[] = $value;
        }

        return $valueList;
    }

    /**
     * @param string $sku
     * @param \Amasty\Finder\Api\Data\FinderOptionInterface[] $dropdowns
     * @return bool
     */
    public function saveOption($sku, $dropdowns)
    {
        $data = ['sku' => $sku];

        foreach ($dropdowns as $dropdown) {
            $data['label_' . $dropdown->getId()] = $dropdown->getValue();
        }

        $this->saveNewFinder($data);

        return true;
    }
}
