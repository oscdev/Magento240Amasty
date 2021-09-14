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
use Magento\Backend\Block\Widget\Form\Generic;
use Amasty\Finder\Controller\Adminhtml\Finder\ExportSampleFile;

class UniversalImport extends Generic implements TabInterface
{
    use \Amasty\Finder\MyTrait\FinderTab;

    /**
     * UniversalImport constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data
    ) {
        $this->model = $registry->registry('current_amasty_finder_finder');
        parent::__construct($context, $registry, $formFactory, $data);
        $this->tabLabel = __('Universal Products Import');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'universal_form',
                    'action' => $this->getUrl('amasty_finder/finder/importUniversal', [
                        'id' => $this->model->getId()
                    ]),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $form->setHtmlIdPrefix('finder_');
        $fieldset = $form->addFieldset('amfinder_universalimport', ['legend' => __('Import Universal Products')]);

        $fieldset->addField(
            'customer_help',
            'label',
            [
                'name' => 'customer_help',
                'note' => __(
                    'To work with universal products please set \'Use universal products\' 
                    to \'Yes\' at <a href="%1">configuration</a>',
                    $this->getUrl('adminhtml/system_config/edit/section/amfinder')
                ),
            ]
        );

        $fieldset->addField(
            'importuniversal_clear',
            'select',
            [
                'name' => 'importuniversal_clear',
                'label' => __('Delete Existing Data'),
                'title' => __('Delete Existing Data'),
                'values' => [
                    ['value' => 0, 'label' => __('No')],
                    ['value' => 1, 'label' => __('Yes')]
                ]
            ]
        );
        $downloadLabel = __('Download Sample File');
        $url = $this->getUrl('*/*/exportSampleFile', [
            'id' => $this->model->getId(),
            'type' => ExportSampleFile::EXAMPLE_UNIVERSAL
        ]);
        $fieldset->addField(
            'importuniversal_file',
            'file',
            [
                'name' => 'importuniversal_file',
                'label' => __('CSV File'),
                'title' => __('CSV File'),
                'required' => false,
                'note' => sprintf('<a href="%s" download>%s</a>', $url, $downloadLabel)
            ]
        );

        $fieldset->addField(
            'importuniversal',
            'button',
            [
                'name' => 'import_universal',
                'title' => __('Import'),
                'value' => __('Import'),
                'required' => false,

            ]
        )->setRenderer($this->getLayout()->createBlock(
            \Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab\Import\Renderer\ImportButton::class
        ));

        $this->setForm($form);
        $form->setUseContainer(true);
        return parent::_prepareForm();
    }
}
