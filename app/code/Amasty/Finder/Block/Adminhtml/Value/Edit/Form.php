<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Block\Adminhtml\Value\Edit;

use Magento\Framework\Exception\NoSuchEntityException;
use Amasty\Finder\Controller\Adminhtml\Value\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    const LABEL = 'label';
    const NAME = 'name';
    const TITLE = 'title';
    const VALIDATE_CLASS = 'class';

    /**
     * @var \Amasty\Finder\Api\ValueRepositoryInterface
     */
    private $valueRepository;

    /**
     * @var \Amasty\Finder\Api\DropdownRepositoryInterface
     */
    private $dropdownRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\Finder\Api\ValueRepositoryInterface $valueRepository,
        \Amasty\Finder\Api\DropdownRepositoryInterface $dropdownRepository,
        array $data
    ) {
        $this->valueRepository = $valueRepository;
        $this->dropdownRepository = $dropdownRepository;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('amasty_finder_value_form');
        $this->setTitle(__('Value Information'));
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Backend\Block\Widget\Form\Generic
     */
    protected function _prepareForm()
    {
        /** @var $value \Amasty\Finder\Model\Value */
        $value = $this->_coreRegistry->registry(Edit::CURRENT_AMASTY_FINDER_VALUE);
        $finder = $this->_coreRegistry->registry('current_amasty_finder_finder');
        $settingData = [];
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('amasty_finder/value/save', [
                        'id' => $this->getRequest()->getParam('id'),
                        'finder_id' => $finder->getId()
                    ]),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldSet = $form->addFieldset('set', ['legend' => __('General')]);
        $fieldSet->addField('sku', 'text', [
            self::LABEL => __('SKU'),
            self::TITLE => __('SKU'),
            self::NAME => 'sku',
        ]);

        if ($value->getId()) {
            $settingData['sku'] = $this->valueRepository->getSkuById(
                $this->getRequest()->getParam('id'),
                $value->getId()
            );
        }
        $currentId = $value->getId();

        $fields = [];
        $valueIds = [];
        while ($currentId) {
            $aliasName = self::NAME . '_' . $currentId;
            $aliasLabel = self::LABEL . '_' . $currentId;
            $valueIds[$aliasName] = $currentId;

            $model = clone $value;
            $model->load($currentId);
            $currentId = $model->getParentId();
            $dropdownId = $model->getDropdownId();
            try {
                $dropdown = $this->dropdownRepository->getById($dropdownId);
                $dropdownName = $dropdown->getName();
            } catch (NoSuchEntityException $e) {
                $dropdownName = __('Undefined');
            }
            $settingData[$aliasName] = $model->getName();
            $fields[$aliasName] = [
                self::LABEL => __($dropdownName),
                self::TITLE => __($dropdownName),
                self::NAME => $aliasLabel,
                self::VALIDATE_CLASS => 'validate-no-empty',
            ];
        }

        $fields = array_reverse($fields);

        try {
            foreach ($fields as $aliasName => $fieldData) {
                if (isset($valueIds[$aliasName])) {
                    $fieldSet->addField($aliasName, 'text', $fieldData);
                    $value = $this->valueRepository->getById($valueIds[$aliasName]);

                    $this->addImageFieldSet(
                        $fieldSet,
                        $value->getDropdownId(),
                        $fieldData['title']->getText(),
                        $value->getImage()
                    );
                }
            }
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }

        if (!$value->getId()) {
            $finder = $value->getFinder();

            foreach ($finder->getDropdowns() as $dropdown) {
                $aliasLabel = self::LABEL . '_' . $dropdown->getId();
                $fieldSet->addField($aliasLabel, 'text', [
                    self::LABEL => __($dropdown->getName()),
                    self::TITLE => __($dropdown->getName()),
                    self::NAME => $aliasLabel,
                    self::VALIDATE_CLASS => 'validate-no-empty',
                ]);

                $this->addImageFieldSet($fieldSet, $dropdown->getId(), $dropdown->getName());
            }

            $fieldSet->addField('new_finder', 'hidden', [self::NAME => 'new_finder']);
            $settingData['new_finder'] = 1;
        }

        $values = count($settingData) > 1
            ? $settingData
            : $this->_coreRegistry->registry(Edit::CURRENT_AMASTY_FINDER_VALUE)->getData();

        //set form values
        $form->setValues($values);

        return parent::_prepareForm();
    }

    /**
     * @param $fieldSet
     * @param $dropdownId
     * @param $dropdownName
     * @param string $imagePath
     */
    private function addImageFieldSet($fieldSet, $dropdownId, $dropdownName, $imagePath = '')
    {
        $fieldSet->addField(
            $dropdownId,
            'file',
            [
                self::NAME => $dropdownId,
                self::LABEL => __('%1 Image', $dropdownName),
                self::TITLE => __('%1 Image', $dropdownName),
                'after_element_html' => $this->addImageView($dropdownId, $imagePath)
            ]
        );
    }

    /**
     * @param $dropdownId
     * @param string $imagePath
     * @return string
     */
    private function addImageView($dropdownId, $imagePath)
    {
        $url = rtrim($this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ), '/');

        $image = '';

        if ($imagePath) {
            $elementName = 'image_delete_' . $dropdownId;

            $image = '<div class="am-img-wrapper">
                <input type="checkbox" id="' . $elementName . '" name="' . $elementName . '" value="1"/>
                <label for="' . $elementName . '">' . __('Delete Image') . '</label>
                <img class="am-img" src="' . $url . $imagePath . '" style="max-width: 100px; max-height: 100px;"/>
                </div>';
        }

        return $image;
    }
}
