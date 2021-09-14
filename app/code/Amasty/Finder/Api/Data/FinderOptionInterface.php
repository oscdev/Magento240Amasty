<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Api\Data;

interface FinderOptionInterface
{
    const ID = 'id';
    const VALUE = 'value';

    /**
     * @return int
     */
    public function getDropdownId();

    /**
     * @param int $id
     * @return mixed
     */
    public function setDropdownId($id);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return mixed
     */
    public function setValue($value);
}
