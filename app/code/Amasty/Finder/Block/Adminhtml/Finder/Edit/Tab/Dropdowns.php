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

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Amasty\Finder\Helper\Import as ImportHelper;
use Amasty\Finder\Model\Source\DisplayType;

class Dropdowns extends Generic implements TabInterface
{
    /** @var \Amasty\Finder\Model\Finder */
    private $model;

    /**
     * @var DisplayType
     */
    private $displayType;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        DisplayType $displayType,
        array $data
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->model = $this->_coreRegistry->registry('current_amasty_finder_finder');
        $this->displayType = $displayType;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Dropdowns');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Dropdowns');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return !$this->model->getId();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('finder_dropdown_');
        $values = [];
        foreach ($this->model->getDropdowns() as $dropdown) {
            $prefix = 'dropdown_' . $dropdown->getId();

            $fieldset = $form->addFieldset($prefix, ['legend' => __('Dropdown #%1', $dropdown->getPos() + 1)]);
            $fieldset->addField(
                $prefix . '_name',
                'text',
                ['name' => $prefix . '_name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
            );
            $values[$prefix . '_name'] = $dropdown->getName();
            $fieldset->addField(
                $prefix . '_sort',
                'select',
                [
                    'name' => $prefix . '_sort', 'label' => __('Sort'), 'title' => __('Sort'), 'required' => true,
                    'values' => [
                        ['value' => ImportHelper::SORT_STRING_ASC, 'label' => __('alphabetically, asc')],
                        ['value' => ImportHelper::SORT_STRING_DESC, 'label' => __('alphabetically, desc')],
                        ['value' => ImportHelper::SORT_NUM_ASC, 'label' => __('numerically, asc')],
                        ['value' => ImportHelper::SORT_NUM_DESC, 'label' => __('numerically, desc')],
                    ]
                ]
            );
            $values[$prefix . '_sort'] = $dropdown->getSort();

            $fieldset->addField(
                $prefix . '_range',
                'select',
                [
                    'name' => $prefix . '_range', 'label' => __('Range'), 'title' => __('Range'), 'required' => true,
                    'values' => [
                        ['value' => 0, 'label' => __('No')],
                        ['value' => 1, 'label' => __('Yes')],
                    ]
                ]
            );
            $values[$prefix . '_range'] = $dropdown->getRange();

            $fieldset->addField(
                $prefix . '_display_type',
                'select',
                [
                    'name' => $prefix . '_display_type',
                    'label' => __('Display Type'),
                    'title' => __('Display Type'),
                    'required' => true,
                    'values' => $this->displayType->toOptionArray()
                ]
            );

            $values[$prefix . '_display_type'] = $dropdown->getDisplayType();
        }
        $form->setValues($values);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
