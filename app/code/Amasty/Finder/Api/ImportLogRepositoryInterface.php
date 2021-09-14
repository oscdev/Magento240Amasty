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
interface ImportLogRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Finder\Api\Data\ImportLogInterface $importLog
     * @return \Amasty\Finder\Api\Data\ImportLogInterface
     */
    public function save(\Amasty\Finder\Api\Data\ImportLogInterface $importLog);

    /**
     * Get by id
     *
     * @param int $id
     * @return \Amasty\Finder\Api\Data\ImportLogInterface
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\Finder\Api\Data\ImportLogInterface $importLog
     * @return bool true on success
     */
    public function delete(\Amasty\Finder\Api\Data\ImportLogInterface $importLog);

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
     * Lists
     *
     * @return \Amasty\Finder\Api\Data\ImportLogInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();

    /**
     * @return \Amasty\Finder\Model\ResourceModel\ImportLog\Collection
     */
    public function getNotLockedFiles();

    /**
     * @param $fileName
     * @param $finderId
     * @return \Magento\Framework\DataObject[]
     */
    public function getByNameAndFinder($fileName, $finderId);

    /**
     * @param $file
     * @param $finderId
     * @return bool
     */
    public function addUniqueFile($file, $finderId);

    /**
     * @param $finderId
     * @return bool
     */
    public function deleteByIdWithoutReplaceFile($finderId);

    /**
     * @param $finderId
     * @return bool
     */
    public function hasIssetReplaceFile($finderId);

    /**
     * @param $tableName
     * @return string
     */
    public function getTable($tableName);

    /**
     * @param $finderId
     * @return bool
     */
    public function deleteByFinderId($finderId);
}
