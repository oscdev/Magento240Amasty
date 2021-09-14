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
interface ImportHistoryRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Finder\Api\Data\ImportHistoryInterface $importLogHistory
     * @return \Amasty\Finder\Api\Data\ImportHistoryInterface
     */
    public function save(\Amasty\Finder\Api\Data\ImportHistoryInterface $importLogHistory);

    /**
     * @param $data
     * @return \Amasty\Finder\Model\ImportHistory
     */
    public function saveData($data);

    /**
     * Get by id
     *
     * @param int $id
     * @return \Amasty\Finder\Api\Data\ImportHistoryInterface
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\Finder\Api\Data\ImportHistoryInterface $importLogHistory
     * @return bool true on success
     */
    public function delete(\Amasty\Finder\Api\Data\ImportHistoryInterface $importLogHistory);

    /**
     * Delete by id
     *
     * @param int $id
     * @return bool true on success
     */
    public function deleteById($id);

    /**
     * @param $finderId
     * @return bool
     */
    public function deleteByFinderId($finderId);

    /**
     * @param $ids
     * @return bool
     */
    public function deleteByIds($ids);

    /**
     * @param $date
     * @return bool
     */
    public function clearLogHistory($date);

    /**
     * Lists
     *
     * @return \Amasty\Finder\Api\Data\ImportHistoryInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();
}
