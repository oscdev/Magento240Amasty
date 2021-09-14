<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Source;

class DisplayType implements \Magento\Framework\Option\ArrayInterface
{
    const DROPDOWN = 0;
    const LABEL = 1;
    const IMAGE_LABEL = 2;
    const IMAGE = 3;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::DROPDOWN, 'label' => __('Dropdown')],
            ['value' => self::LABEL, 'label' => __('Label')],
            ['value' => self::IMAGE_LABEL, 'label' => __('Image and Label')],
            ['value' => self::IMAGE, 'label' => __('Image')]
        ];
    }
}
