<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab\Import\Renderer;

class ImportErrors extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function _getValue(\Magento\Framework\DataObject $row)
    {
        $value = parent::_getValue($row);

        if ($value > 0) {
            $url = $this->getUrl('*/finder/errors', ['file_id' => $row->getId(), 'file_state' => $row->getFileState()]);
            $value .= ' <a class="show-import-errors" data-url="' . $url . '" href="#">' . __('View') . '</a>';
        }

        return $value;
    }
}
