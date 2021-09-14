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
interface ValueRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Finder\Api\Data\ValueInterface $value
     * @return \Amasty\Finder\Api\Data\ValueInterface
     */
    public function save(\Amasty\Finder\Api\Data\ValueInterface $value);

    /**
     * @param $parentId
     * @param $dropdownId
     * @param $name
     * @return bool
     */
    public function saveValue($parentId, $dropdownId, $name);

    /**
     * @param array $data
     * @return int
     */
    public function saveNewFinder(array $data);

    /**
     * @param $finder
     * @param $file
     * @return array
     */
    public function importImages($finder, $file);

    /**
     * @return \Amasty\Finder\Api\Data\ValueInterface
     */
    public function getValueModel();

    /**
     * Get by id
     *
     * @param int $id
     * @return \Amasty\Finder\Api\Data\ValueInterface
     */
    public function getById($id);

    /**
     * @param $parentId
     * @param $dropdownId
     * @return \Amasty\Finder\Model\ResourceModel\Value\Collection
     */
    public function getByParentAndDropdownIds($parentId, $dropdownId);

    /**
     * @param $id
     * @return \Amasty\Finder\Api\Data\ValueInterface
     */
    public function getByParentId($id);

    /**
     * @param $newId
     * @param $finderI
     * @return string
     */
    public function getSkuById($newId, $finderI);

    /**
     * Delete
     *
     * @param \Amasty\Finder\Api\Data\ValueInterface $value
     * @return bool true on success
     */
    public function delete(\Amasty\Finder\Api\Data\ValueInterface $value);

    /**
     * @param int $id
     * @param int $finder
     * @return bool
     */
    public function deleteById($id, $finder);

    /**
     * @param array $ids
     * @param \Amasty\Finder\Api\Data\FinderInterface $finder
     * @return bool
     */
    public function deleteByIds($ids, $finder);

    /**
     * @param \Amasty\Finder\Api\Data\FinderInterface $finder
     * @return bool
     */
    public function deleteOldData($finder);

    /**
     * @param int $id
     * @return bool
     */
    public function deleteOnlyValue($id);

    /**
     * Lists
     *
     * @return \Amasty\Finder\Api\Data\ValueInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();

    /**
     * @param string $sku
     * @param \Amasty\Finder\Api\Data\FinderOptionInterface[] $dropdowns
     * @return bool
     */
    public function saveOption($sku, $dropdowns);
}
