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
interface UniversalRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Finder\Api\Data\UniversalInterface $universal
     * @return \Amasty\Finder\Api\Data\UniversalInterface
     */
    public function save(\Amasty\Finder\Api\Data\UniversalInterface $universal);

    /**
     * Get by id
     *
     * @param int $id
     * @return \Amasty\Finder\Api\Data\UniversalInterface
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\Finder\Api\Data\UniversalInterface $universal
     * @return bool true on success
     */
    public function delete(\Amasty\Finder\Api\Data\UniversalInterface $universal);

    /**
     * Delete by id
     *
     * @param int $id
     * @return bool true on success
     */
    public function deleteById($id);

    /**
     * @param $ids
     * @return bool
     */
    public function deleteByIds($ids);

    /**
     * Lists
     *
     * @return \Amasty\Finder\Api\Data\UniversalInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();
}
