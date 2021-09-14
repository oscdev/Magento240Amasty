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

class Grid extends \Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab\AbstractGrid
{
    /**
     * @return \Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab\AbstractGrid
     */
    protected function _prepareCollection()
    {
        $finder = $this->getFinder();

        if ($finder->getId()) {
            $this->importModel->loadNewFilesFromFtp($finder->getId());
        }

        $this->importCollection->addFieldToFilter('finder_id', $finder->getId())->orderForImport();
        $this->setCollection($this->importCollection);

        return parent::_prepareCollection();
    }
}
