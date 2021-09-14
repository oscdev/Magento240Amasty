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
interface DropdownRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Finder\Api\Data\DropdownInterface $dropdown
     * @return \Amasty\Finder\Api\Data\DropdownInterface
     */
    public function save(\Amasty\Finder\Api\Data\DropdownInterface $dropdown);

    /**
     * Get by id
     *
     * @param int $id
     * @return \Amasty\Finder\Api\Data\DropdownInterface
     */
    public function getById($id);

    /**
     * @return \Amasty\Finder\Model\Dropdown
     */
    public function getDropdownModel();

    /**
     * @param $finderId
     * @return array
     */
    public function getByFinderId($finderId);

    /**
     * Delete
     *
     * @param \Amasty\Finder\Api\Data\DropdownInterface $dropdown
     * @return bool true on success
     */
    public function delete(\Amasty\Finder\Api\Data\DropdownInterface $dropdown);

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
     * @return \Amasty\Finder\Api\Data\DropdownInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList();
}
