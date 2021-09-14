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

class Universal extends \Magento\Backend\Block\Widget\Grid\Container implements TabInterface
{
    use \Amasty\Finder\MyTrait\FinderTab;

    /**
     * Universal constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data
    ) {
        $this->model = $registry->registry('current_amasty_finder_finder');
        $this->_controller = 'finder';
        $this->_headerText = __('Universal Products');
        parent::__construct($context, $data);
        $this->tabLabel = __('Universal Products');
        $this->removeButton('add');
    }

    /**
     * @param null $region
     * @return null
     */
    public function getButtonsHtml($region = null)
    {
        return null;
    }
}
