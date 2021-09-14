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

use Amasty\Base\Model\MagentoVersion;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Amasty\Finder\Model\Source\Category as CategorySource;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Config\Model\Config\Source\Yesno;

class Main extends Generic implements TabInterface
{
    /**
     * @var CategorySource
     */
    protected $categorySource;

    /**
     * @var Yesno
     */
    private $yesno;

    /**
     * @var MagentoVersion
     */
    private $magentoVersion;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CategorySource $categorySource,
        Yesno $yesno,
        MagentoVersion $magentoVersion,
        array $data = []
    ) {
        $this->categorySource = $categorySource;
        $this->yesno = $yesno;
        $this->magentoVersion = $magentoVersion;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General');
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
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Generic
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_finder_finder');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('finder_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Title'), 'title' => __('Title'), 'required' => true]
        );

        if (!$model->getId()) {
            $fieldset->addField(
                'cnt',
                'text',
                [
                    'name' => 'cnt',
                    'label' => __('Number of Dropdowns'),
                    'title' => __('Number of Dropdowns'),
                    'class' => 'validate-greater-than-zero',
                    'required' => true
                ]
            );
        }

        $fieldset->addField(
            'template',
            'select',
            [
                'name' => 'template',
                'label' => __('Template'),
                'title' => __('Template'),
                'required' => false,
                'values' => [
                    ['value' => 'horizontal', 'label' => __('Horizontal')],
                    ['value' => 'vertical', 'label' => __('Vertical')]
                ],
                'note' => __('The `horizontal` option is responsive and set by default')
            ]
        );

        $fieldset->addField(
            'custom_url',
            'text',
            [
                'name' => 'custom_url',
                'label' => __('Custom Destination URL'),
                'title' => __('Custom Destination URL'),
                'required' => false,
                'note' =>
                    __(
                        'It determines the page the Finder will redirect customers to when the Find button is pressed. 
                        Enter category URL, e.g. special-category.html to redirect search results to one category.'
                    )
            ]
        );

        $fieldset->addField(
            'default_category',
            'select',
            [
                'name' => 'default_category',
                'label' => __('Add Finder to the Default Category'),
                'title' => __('Add Finder to the Default Category'),
                'required' => false,
                'values' => $this->yesno->toOptionArray(),
                'note' =>
                    __(
                        'Keep \'Yes\' to get the Finder working properly at the home and cms pages.'
                    )
            ]
        );

        $fieldset->addField(
            'hide_finder',
            'select',
            [
                'name' => 'hide_finder',
                'label' => __('Finder block visibility on the Default Category'),
                'title' => __('Finder block visibility on the Default Category'),
                'required' => false,
                'values' => [
                    ['value' => 0, 'label' => __('Hide')],
                    ['value' => 1, 'label' => __('Show')]
                ],
                'note' =>
                    __(
                        'Keep \'Hide\' to not display this finder block on the default category on the frontend. 
                        It is useful when there are several finders added to the default category but you need to 
                        display only one of them at the frontend.'
                    )
            ]
        );

        $fieldset->addField(
            'search_page',
            'select',
            [
                'name' => 'search_page',
                'label' => __('Add Finder to the Search Page'),
                'title' => __('Add Finder to the Search Page'),
                'required' => false,
                'values' => $this->yesno->toOptionArray()
            ]
        );

        $fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Add the finder block in'),
                'title' => __('Add the finder block in'),
                'required' => false,
                'note' =>
                    __(
                        'Place the product finder in particular themes, categories, and other locations.
                        Possible options: sidebar.main, content, content.top, footer.'
                    )
            ]
        );

        $fieldset->addField(
            'categories',
            'multiselect',
            [
                'name' => 'categories',
                'label' => __('Categories'),
                'title' => __('Categories'),
                'style' => 'height: 500px; width: 300px;',
                'values' => $this->categorySource->toOptionArray(),
            ]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'code_for_inserting',
                'textarea',
                [
                    'label' => __('Code for inserting'),
                    'title' => __('Code for inserting'),
                    'disabled' => 1,
                    'note' =>
                        'Use this code if you want to put product finder in any CMS page or block.'
                ]
            );
            $fieldSettings = [
                'label' => __('Code for inserting in Layout Update XML'),
                'title' => __('Code for inserting in Layout Update XML'),
                'disabled' => 1,
            ];

            if (version_compare($this->magentoVersion->get(), '2.3.4', '<')) {
                $fieldSettings['note'] = 'To add a product finder to a category manually, navigate to 
                        Catalog > Categories > Select your category (i.e. Wheels). 
                        In the Design section find Layout Update XML field and paste the code there.';
            }

            $fieldset->addField(
                'code_for_inserting_in_layout',
                'textarea',
                $fieldSettings
            );
        }

        if (!$model->getId()) {
            $form->addValues(
                [
                    'default_category' => 1,
                    'hide_finder' => 0
                ]
            );
        }

        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Form\Element\Dependence::class)
                ->addFieldMap($htmlIdPrefix . 'default_category', 'default_category')
                ->addFieldMap($htmlIdPrefix . 'hide_finder', 'hide_finder')
                ->addFieldDependence('hide_finder', 'default_category', 1)
        );

        $form->setValues($model->getData());
        $form->addValues(
            [
                'id' => $model->getId(),
                'code_for_inserting' =>
                    '{{block class="Amasty\\Finder\\Block\\Form" block_id="finder_form" '
                    . 'location="cms" id="' . $model->getId() . '"}}',
                'code_for_inserting_in_layout' => $model->getFinderXmlCode()
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
