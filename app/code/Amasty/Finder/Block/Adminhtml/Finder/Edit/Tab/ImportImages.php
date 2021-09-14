<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Form\Generic;
use Amasty\Finder\Controller\Adminhtml\Finder\ExportSampleFile;

class ImportImages extends Generic implements TabInterface
{
    use \Amasty\Finder\MyTrait\FinderTab;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data
    ) {
        $this->model = $registry->registry('current_amasty_finder_finder');
        parent::__construct($context, $registry, $formFactory, $data);
        $this->tabLabel = __('Add Images To The Filter Options');
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
                    'id' => 'images_form',
                    'action' => $this->getUrl('amasty_finder/finder/importImages', [
                        'id' => $this->model->getId()
                    ]),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $form->setHtmlIdPrefix('finder_');
        $fieldset = $form->addFieldset('amfinder_imageimport', ['legend' => __('Import Images')]);

        $fieldset->addField(
            'directory_info',
            'label',
            [
                'name' => 'directory_info',
                'note' => __(
                    'For import please upload images to the following directory: pub/media/amasty/finder/images_tmp'
                ),
            ]
        );
        $downloadLabel = __('Download Sample File');
        $url = $this->getUrl('*/*/exportSampleFile', [
            'id' => $this->model->getId(),
            'type' => ExportSampleFile::EXAMPLE_IMAGE
        ]);
        $fieldset->addField(
            'importimages_file',
            'file',
            [
                'name' => 'importimages_file',
                'label' => __('CSV File'),
                'title' => __('CSV File'),
                'required' => false,
                'note' => sprintf('<a href="%s" download>%s</a>', $url, $downloadLabel)
            ]
        );

        $fieldset->addField(
            'importimage',
            'button',
            [
                'name' => 'import_image',
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

    /**
     * @return string
     */
    public function toHtml()
    {
        $html = parent::toHtml();
        $html = str_replace('name="importimages_file"', 'name="importimages_file" accept=".csv"', $html);
        $html = str_replace('name="importuniversal_file"', 'name="importuniversal_file" accept=".csv"', $html);
        return $html;
    }
}
