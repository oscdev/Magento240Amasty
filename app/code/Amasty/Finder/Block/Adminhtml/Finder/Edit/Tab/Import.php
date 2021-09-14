<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class Import extends \Magento\Backend\Block\Template implements TabInterface
{
    use \Amasty\Finder\MyTrait\FinderTab;

    /**
     * Import constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data
    ) {
        $this->model = $registry->registry('current_amasty_finder_finder');
        parent::__construct($context, $data);
        $this->tabLabel = __('Import');
    }

    /**
     * @return int
     */
    public function getFinderId()
    {
        return $this->model->getId();
    }
}
