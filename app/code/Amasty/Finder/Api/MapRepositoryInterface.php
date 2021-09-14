<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Api;

/**
 * @api
 */
interface MapRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Finder\Api\Data\MapInterface $map
     * @return \Amasty\Finder\Api\Data\MapInterface
     */
    public function save(\Amasty\Finder\Api\Data\MapInterface $map);

    /**
     * @param int $valueId
     * @param string $sku
     * @return bool
     */
    public function saveMap($valueId, $sku);

    /**
     * Get by id
     *
     * @param int $id
     * @return \Amasty\Finder\Api\Data\MapInterface
     */
    public function getById($id);

    /**
     * @param $id
     * @return \Amasty\Finder\Model\Map
     */
    public function getByValueId($id);

    /**
     * Delete
     *
     * @param \Amasty\Finder\Api\Data\MapInterface $map
     * @return bool true on success
     */
    public function delete(\Amasty\Finder\Api\Data\MapInterface $map);

    /**
     * Delete by id
     *
     * @param int $id
     * @return bool true on success
     */
    public function deleteById($id);

    /**
     * Lists
     *
     * @return \Amasty\Finder\Api\Data\MapInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();

    /**
     * @param $productId
     * @param $dropdowns
     * @return \Amasty\Finder\Model\ResourceModel\Map\Collection
     */
    public function getDependsValues($productId, $dropdowns);
}
