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

class Products extends \Magento\Backend\Block\Widget\Grid\Container implements TabInterface
{
    use \Amasty\Finder\MyTrait\FinderTab;

    /**
     * Products constructor.
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
        parent::__construct($context, $data);
        $this->tabLabel = __('Products');
    }

    protected function _construct()
    {
        $this->_controller = 'finder';
        $this->_headerText = __('Products');
        $this->_addButtonLabel = __('Add New Product');
        parent::_construct();
        $this->addButton(
            'add_new',
            [
                'label' => $this->getAddButtonLabel(),
                'onclick' => 'setLocation(\'' . $this->getCreateUrl() . '\')',
                'class' => 'add primary'
            ],
            0,
            0,
            $this->getNameInLayout()
        );
        $this->removeButton('add');

        $this->addButton(
            'remove_all',
            [
                'label' => __('Remove all products'),
                'onclick' => 'deleteConfirm(\'' . __('Are you sure?') . '\',\'' .
                    $this->getUrl('*/finder/removeAllProducts', ['id' => $this->model->getId()]) . '\')',
                'class' => 'delete'
            ],
            0,
            0,
            $this->getNameInLayout()
        );
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/value/new', ['finder_id' => $this->model->getId()]);
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/*/products', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }
}
