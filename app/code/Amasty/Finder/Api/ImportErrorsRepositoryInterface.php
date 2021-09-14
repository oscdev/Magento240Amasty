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
interface ImportErrorsRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Finder\Api\Data\ImportErrorsInterface $importErrors
     * @return \Amasty\Finder\Api\Data\ImportErrorsInterface
     */
    public function save(\Amasty\Finder\Api\Data\ImportErrorsInterface $importErrors);

    /**
     * @param $fileId
     * @param $historyFileId
     * @return $historyFileId
     */
    public function archiveErrorHistory($fileId, $historyFileId);

    /**
     * Get by id
     *
     * @param int $id
     * @return \Amasty\Finder\Api\Data\ImportErrorsInterface
     */
    public function getById($id);

    /**
     * @param $field
     * @param $id
     * @return \Amasty\Finder\Model\ResourceModel\ImportErrors\Collection
     */
    public function getErrorsCollection($field, $id);

    /**
     * Delete
     *
     * @param \Amasty\Finder\Api\Data\ImportErrorsInterface $importErrors
     * @return bool true on success
     */
    public function delete(\Amasty\Finder\Api\Data\ImportErrorsInterface $importErrors);

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
     * @return \Amasty\Finder\Api\Data\ImportErrorsInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();
}
