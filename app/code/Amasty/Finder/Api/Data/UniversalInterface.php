<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Api\Data;

interface UniversalInterface
{
    /**
     * Constants defined for keys of data array
     */
    const UNIVERSAL_ID = 'universal_id';
    const FINDER_ID = 'finder_id';
    const SKU = 'sku';
    const PID = 'pid';

    /**
     * @return int
     */
    public function getUniversalId();

    /**
     * @param int $universalId
     *
     * @return $this
     */
    public function setUniversalId($universalId);

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
     * @return string
     */
    public function getSku();

    /**
     * @param string $sku
     *
     * @return $this
     */
    public function setSku($sku);

    /**
     * @return int
     */
    public function getPid();

    /**
     * @param int $pid
     *
     * @return $this
     */
    public function setPid($pid);
}
