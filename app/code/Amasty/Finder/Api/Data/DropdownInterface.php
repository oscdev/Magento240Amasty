<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Api\Data;

interface DropdownInterface
{
    /**
     * Constants defined for keys of data array
     */
    const DROPDOWN_ID = 'dropdown_id';
    const FINDER_ID = 'finder_id';
    const POS = 'pos';
    const NAME = 'name';
    const SORT = 'sort';
    const RANGE = 'range';
    const DISPLAY_TYPE = 'display_type';

    /**
     * @return int
     */
    public function getDropdownId();

    /**
     * @param int $dropdownId
     *
     * @return $this
     */
    public function setDropdownId($dropdownId);

    /**
     * @return int
     */
    public function getFinderId();

    /**
     * @param int $finderId
     *
     * @return $this
     */
    public function setFinderId($finderId);

    /**
     * @return int
     */
    public function getPos();

    /**
     * @param int $pos
     *
     * @return $this
     */
    public function setPos($pos);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return int
     */
    public function getSort();

    /**
     * @param int $sort
     *
     * @return $this
     */
    public function setSort($sort);

    /**
     * @return int
     */
    public function getRange();

    /**
     * @param int $range
     *
     * @return $this
     */
    public function setRange($range);

    /**
     * @return int
     */
    public function getDisplayType();

    /**
     * @param int $displayType
     *
     * @return $this
     */
    public function setDisplayType($displayType);
}
