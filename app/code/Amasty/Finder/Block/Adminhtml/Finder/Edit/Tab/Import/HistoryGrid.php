<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab\Import;

class HistoryGrid extends \Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab\AbstractGrid
{
    /**
     * @return \Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab\AbstractGrid
     */
    protected function _prepareCollection()
    {
        $finder = $this->getFinder();
        $this->importHistoryCollection->addFieldToFilter('finder_id', $finder->getId());
        $this->setCollection($this->importHistoryCollection);
        return parent::_prepareCollection();
    }
}
