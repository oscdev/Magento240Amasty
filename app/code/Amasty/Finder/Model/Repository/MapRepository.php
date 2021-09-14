<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Repository;

use Amasty\Finder\Api\Data\MapInterface;
use Amasty\Finder\Api\MapRepositoryInterface;
use Amasty\Finder\Model\MapFactory;
use Amasty\Finder\Model\ResourceModel\Map;
use Amasty\Finder\Model\ResourceModel\Map\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class MapRepository implements MapRepositoryInterface
{
    /**
     * @var MapFactory
     */
    private $mapFactory;
    
    /**
     * @var Map
     */
    private $mapResource;
    
    /**
     * @var array
     */
    private $maps;
    
    /**
     * @var CollectionFactory
     */
    private $mapCollectionFactory;

    /**
     * MapRepository constructor.
     * @param MapFactory $mapFactory
     * @param Map $mapResource
     * @param CollectionFactory $mapCollectionFactory
     */
    public function __construct(
        MapFactory $mapFactory,
        Map $mapResource,
        CollectionFactory $mapCollectionFactory
    ) {
        $this->mapFactory = $mapFactory;
        $this->mapResource = $mapResource;
        $this->mapCollectionFactory = $mapCollectionFactory;
    }

    /**
     * @param MapInterface $map
     * @return MapInterface
     * @throws CouldNotSaveException
     */
    public function save(MapInterface $map)
    {
        try {
            $this->mapResource->save($map);
        } catch (\Exception $e) {
            if ($map->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save map with ID %1. Error: %2',
                        [$map->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new map. Error: %1', $e->getMessage()));
        }

        return $map;
    }

    /**
     * @param $valueId
     * @param $sku
     * @return bool
     */
    public function saveMap($valueId, $sku)
    {
        $connection = $this->mapResource->getConnection();
        $connection->insertOnDuplicate($this->mapResource->getTable('amasty_finder_map'), [
            'value_id' => $valueId,
            'sku' => $sku
        ]);

        return true;
    }

    /**
     * @param int $id
     * @return \Amasty\Finder\Api\Data\MapInterface
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        if (!isset($this->maps[$id])) {
            /** @var \Amasty\Finder\Model\Map $map */
            $map = $this->mapFactory->create();
            $this->mapResource->load($map, $id);
            if (!$map->getId()) {
                throw new NoSuchEntityException(__('map with specified ID "%1" not found.', $id));
            }
            $this->maps[$id] = $map;
        }

        return $this->maps[$id];
    }

    /**
     * @param $id
     * @return \Amasty\Finder\Model\Map
     */
    public function getByValueId($id)
    {
        $map = $this->mapFactory->create();
        $this->mapResource->load($map, $id, 'value_id');
        return $map;
    }

    /**
     * @param MapInterface $map
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(MapInterface $map)
    {
        try {
            $this->mapResource->delete($map);
            unset($this->maps[$map->getId()]);
        } catch (\Exception $e) {
            if ($map->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove map with ID %1. Error: %2',
                        [$map->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove map. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $mapModel = $this->getById($id);
        $this->delete($mapModel);

        return true;
    }

    /**
     * @return array
     */
    public function getList()
    {
        /** @var \Amasty\Finder\Model\ResourceModel\Map\Collection $mapCollection */
        $mapCollection = $this->mapCollectionFactory->create();
        $mapList = [];

        foreach ($mapCollection as $map) {
            $mapList[] = $map;
        }

        return $mapList;
    }

    /**
     * @param $productId
     * @param $dropdowns
     * @return Map\Collection
     */
    public function getDependsValues($productId, $dropdowns)
    {
        return $this->mapCollectionFactory->create()->getDependsValues($productId, $dropdowns);
    }
}
