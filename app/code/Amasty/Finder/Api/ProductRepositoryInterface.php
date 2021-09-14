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
interface ProductRepositoryInterface
{
    /**
     * @param \Amasty\Finder\Api\Data\FinderOptionInterface[] $finderOptions
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProductsByFinderValues($finderOptions);
}
