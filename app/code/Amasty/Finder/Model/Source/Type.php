<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Finder\Model\Source;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    const ONE_DROPDOWN_SELECTED = 1;
    const ALL_DROPDOWNS_SELECTED = 0;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ONE_DROPDOWN_SELECTED, 'label' => __('At Least One Finder Value is Selected')],
            ['value' => self::ALL_DROPDOWNS_SELECTED, 'label' => __('All Finder Values are Selected')]
        ];
    }
}
