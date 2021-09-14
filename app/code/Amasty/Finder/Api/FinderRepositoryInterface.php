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
interface FinderRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Finder\Api\Data\FinderInterface $finder
     * @return \Amasty\Finder\Api\Data\FinderInterface
     */
    public function save(\Amasty\Finder\Api\Data\FinderInterface $finder);

    /**
     * @param $mapId
     * @return bool
     */
    public function isDeletable($mapId);

    /**
     * Get by id
     *
     * @param int $id
     * @return \Amasty\Finder\Api\Data\FinderInterface
     */
    public function getById($id);

    /**
     * @param $finder
     * @param $file
     * @return array
     */
    public function importUniversal($finder, $file);

    /**
     * @return \Amasty\Finder\Model\Finder
     */
    public function getFinderModel();

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
    );

    /**
     * @param $id
     * @return array
     */
    public function getWithoutId($id);

    /**
     * Delete
     *
     * @param \Amasty\Finder\Api\Data\FinderInterface $finder
     * @return bool true on success
     */
    public function delete(\Amasty\Finder\Api\Data\FinderInterface $finder);

    /**
     * @param $ids
     * @return bool
     */
    public function deleteByIds($ids);

    /**
     * Delete by id
     *
     * @param int $id
     * @return bool true on success
     */
    public function deleteById($id);

    /**
     * @param $ids
     * @return \Amasty\Finder\Model\ResourceModel\Finder\Collection
     */
    public function getFindersByIds($ids);

    /**
     * Lists
     *
     * @return \Amasty\Finder\Api\Data\FinderInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();

    /**
     * @return array
     */
    public function updateLinks();

    /**
     * @return \Amasty\Finder\Model\ResourceModel\Finder\Collection
     */
    public function getFindersOnDefaultCategory();

    /**
     * @return \Amasty\Finder\Model\ResourceModel\Finder\Collection
     */
    public function getFindersOnSearchPage();

    /**
     * @param int $categoryId
     * @return mixed
     */
    public function getFindersCategory($categoryId);
}
