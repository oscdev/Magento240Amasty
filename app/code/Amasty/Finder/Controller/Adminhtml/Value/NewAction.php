<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Finder\Controller\Adminhtml\Value;

class NewAction extends \Amasty\Finder\Controller\Adminhtml\Value
{
    /**
     * Dispatch request
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
