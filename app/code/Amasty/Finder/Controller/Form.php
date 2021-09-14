<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Block;

use Amasty\Finder\Model\Finder;
use Amasty\Finder\Model\Source\DisplayType;
use Amasty\Finder\Model\ResourceModel\Value\CollectionFactory;

class Form extends \Magento\Framework\View\Element\Template
{
    const SIZE_FOR_BUTTONS = 1;

    const HORIZONTAL = 'horizontal';
    const ALL_SIZE = '100';

    /**
     * @var bool
     */
    private $isApplied = false;

    /**
     * @var \Amasty\Finder\Model\Finder
     */
    private $finder;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    private $catalogLayer;

    /**
     * @var \Magento\Framework\Url\Encoder
     */
    private $urlEncoder;

    /**
     * @var int
     */
    private $parentDropdownId = 0;

    /**
     * @var \Amasty\Finder\Api\FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * @var \Amasty\Finder\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Amasty\Finder\Model\Dropdown
     */
    private $dropdownModel;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Url\Encoder $urlEncoder,
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository,
        \Amasty\Finder\Helper\Config $configHelper,
        \Amasty\Finder\Model\Dropdown $dropdownModel,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->catalogLayer = $layerResolver->get();
        $this->urlEncoder = $urlEncoder;
        $this->finderRepository = $finderRepository;
        $this->configHelper = $configHelper;
        $this->dropdownModel = $dropdownModel;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
        $this->apply();
    }

    /** @return \Amasty\Finder\Model\Finder */
    public function getFinder()
    {
        if ($this->finder === null) {
            $this->finder = $this->finderRepository->getById($this->getId());
        }
        return $this->finder;
    }

    /**
     * @return bool
     */
    public function isButtonsVisible()
    {
        $cnt = count($this->getFinder()->getDropdowns());

        // we have just 1 dropdown. show the button
        if (self::SIZE_FOR_BUTTONS == $cnt) {
            return true;
        }

        $partialSearch = !!$this->configHelper->getConfigValue('general/partial_search');

        // at least one value is selected and we allow partial search
        if ($this->getFinder()->getSavedValue('current') && $partialSearch) {
            return true;
        }

        // all values are selected.
        if (($this->getFinder()->getSavedValue(Finder::LAST_DROPDOWN))) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    private function getAjaxUrl()
    {
        $isCurrentlySecure = (bool)$this->_storeManager->getStore()->isCurrentlySecure();
        $secure = $isCurrentlySecure ? true : false;
        $url = $this->getUrl('amfinder/index/options', ['_secure' => $secure]);

        return $url;
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        $securedFlag = $this->_storeManager->getStore()->isCurrentlySecure();
        $secured = ['_secure' => $securedFlag];

        $url = $this->getUrl('amfinder', $secured);

        if ($customUrl = $this->getCustomUrl($secured)) {
            return $this->formatUrl($customUrl);
        }

        $category = $this->coreRegistry->registry('current_category');

        if ($this->coreRegistry->registry('current_product')) {
            return $this->formatUrl($url);
        }

        if ($category && $category->getDisplayMode() == \Magento\Catalog\Model\Category::DM_PAGE) {
            return $this->formatUrl($url);
        }

        $url = $this->_urlBuilder->getCurrentUrl();

        return $this->formatUrl($url);
    }

    /**
     * @param \Amasty\Finder\Model\Dropdown $dropdown
     *
     * @return string
     */
    public function getDropdownAttributes(\Amasty\Finder\Model\Dropdown $dropdown)
    {
        $html = sprintf(
            'id="finder-%d--%d" data-dropdown-id="%d"',
            $this->getId(),
            $dropdown->getId(),
            $dropdown->getId()
        );

        if (DisplayType::DROPDOWN === (int)$dropdown->getDisplayType()) {
            $html .= sprintf(
                'name="finder[%d]"',
                $dropdown->getId()
            );
        }

        $parentValueId = $this->getFinder()->getSavedValue($this->getParentDropdownId());
        $currentValueId = $this->getFinder()->getSavedValue($dropdown->getId());

        if ($this->dropdownModel->isHidden($dropdown, $this->getFinder()) && !$parentValueId && !$currentValueId) {
            $html .= 'disabled = "disabled"';
        }

        return $html;
    }

    /**
     * @param $secured
     * @return bool|string
     */
    private function getCustomUrl($secured)
    {
        $customUrl = $this->getFinder()->getCustomUrl()
            ?: $this->configHelper->getConfigValue('general/custom_category');

        $url = false;
        if ($customUrl) {
            $url = $this->_urlBuilder->getCurrentUrl();

            if (strpos($url, $customUrl) === false) {
                $url = $this->getUrl($customUrl, $secured);
            }
        }

        if (!$customUrl && $this->_request->getFullActionName() == 'cms_index_index') {
            $url = $this->getUrl('amfinder', $secured);
        }

        return trim($url, "/");
    }

    /**
     * @return string
     */
    public function getResetUrl()
    {
        if ($this->configHelper->getConfigValue('general/reset_home') == 'current' ||
            $this->_request->getFullActionName() == 'cms_index_index'
        ) {
            return $this->formatUrl($this->_urlBuilder->getCurrentUrl());
        } else {
            return $this->getBackUrl();
        }
    }

    /**
     * @return string
     */
    public function getActionUrl()
    {
        $securedFlag = $this->_storeManager->getStore()->isCurrentlySecure();
        $url = $this->getUrl('amfinder/index/search', ['_secure' => $securedFlag]);

        return $url;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    protected function _toHtml()
    {
        $finderId = $this->getId();
        if (!$finderId) {
            return __('Please specify the Parts Finder ID');
        }

        $finder = $this->getFinder();
        if (!$finder->getId()) {
            return __('Please specify an existing Parts Finder ID');
        }

        if (!$this->coreRegistry->registry($finderId)) {
            $this->coreRegistry->register($finderId, true);
        } else {
            return false;
        }

        $this->setLocation($this->getLocation() . $this->coreRegistry->registry('cms_amfinder'));

        return parent::_toHtml();
    }


    /* BOF code for gettinging value id */

    public function getFinderValueId($data)
    {
        $dataval = trim($data);
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect('value_id');
        $collection->addFieldToSelect('parent_id');
        $collection->addFieldToFilter('name',$dataval);
        $finderValueData = [];
        if (count($collection) > 0) {
            foreach ($collection as $collectionItem) {
                $finderValueData[$collectionItem->getValueId()] = ['value_id' => $collectionItem->getValueId(),'parent_id' => $collectionItem->getParentId()];
            }
        }
        return $finderValueData;
    }

    /**
     * @param $year
     * @return array
     */
    public function getSeachFinderYearData($year)
    {
        $finderYearData = [];
        $searchFinderData = $this->getFinderValueId($year);
        foreach ($searchFinderData as $finderData) {
            $finderYearData[$finderData['value_id']] = ['value_id'=>$finderData['value_id'],'parent_id'=>$finderData['parent_id']];
        }
        return $finderYearData;
    }

    /**
     * @param $model
     * @return array
     */
    public function getSeachFinderModelData($model)
    {
        $finderModelData = [];
        $searchFinderData = $this->getFinderValueId($model);
        foreach ($searchFinderData as $finderData) {
            $finderModelData[$finderData['value_id']] = ['value_id'=>$finderData['value_id'],'parent_id'=>$finderData['parent_id']];
        }
        return $finderModelData;
    }

    /**
     * @param $make
     * @return array
     */
    public function getSeachFinderMakeData($make)
    {
        $finderMakeData = [];
        $searchFinderData = $this->getFinderValueId($make);
        foreach ($searchFinderData as $finderData) {
            $finderMakeData[$finderData['value_id']] = ['value_id'=>$finderData['value_id'],'parent_id'=>$finderData['parent_id']];
        }
        return $finderMakeData;
    }

    /* EOF code for Getting value id */

    /**
     * @return $this
     */
    private function apply()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/apply.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        if ($this->isApplied) {
            return $this;
        }

        $this->_template = 'amfinder.phtml';

        $this->isApplied = true;
        $finder = $this->getFinder();

        /* BOF Manohar Code */
        $logger->info($this->getRequest()->getParam('find'));
        $logger->info($this->getRequest()->getPathInfo());
        $pathInfo = trim($this->getRequest()->getPathInfo(), '/');
        $logger->info("form fine params :".$this->getCurrentApplyUrl());
        $logger->info("form fine params :".print_r($this->getRequest()->getParams(),1));
        if(!empty($this->getRequest()->getParam('find')))
        {
            $finddropdownParam = $this->getRequest()->getParam('find');
            $array2['find'] = $finddropdownParam;
            $logger->info("queryparamstring" . print_r($finddropdownParam, true));
            $this->getRequest()->setParams($array2);
            $urlParam = $this->getRequest()->getParam('find');
        }else {
            $search = 'exhaust';
            $replace = '';
            $subject = $pathInfo;
            $result = str_replace($search, $replace, $subject);
            echo $result;

            $search = '--systems';
            $replace = '';
            $subject = $result;
            $yearMakeModelParam = str_replace($search, $replace, $subject);
            /* EOF Manohar Code */

            /* BOF Anup Code for get Value Id */
            $finderValueId = "";
            $yearData = "";
            $makeData = "";
            $modelData = "";
            $paramData = explode("-", $yearMakeModelParam);
            if (count($paramData) == 3) {
                $yearData = $paramData[0];
                $makeData = $paramData[1];
                $modelData = $paramData[2];
                $finderYearData = $this->getSeachFinderYearData($yearData);
                $finderMakeData = $this->getSeachFinderMakeData($makeData);
                $finderModelData = $this->getSeachFinderModelData($modelData);
                foreach ($finderYearData as $yearData) {
                    foreach ($finderMakeData as $makeData) {
                        foreach ($finderModelData as $modelData) {
                            if (in_array($makeData['value_id'], array($modelData['parent_id']))) {
                                $finderValueId = $modelData['value_id'];
                            }
                        }
                    }
                }

                $logger->info("finderYearData case 3" . print_r($finderYearData, true));
                $logger->info("finderMakeData case 3" . print_r($finderMakeData, true));
                $logger->info("finderModelData case 3" . print_r($finderModelData, true));
                $logger->info("finderValueId case 3" . print_r($finderValueId, true));

            } elseif (count($paramData) == 2) {

                $makeData = $paramData[0];
                $modelData = $paramData[1];
                $finderMakeData = $this->getSeachFinderMakeData($makeData);
                $finderModelData = $this->getSeachFinderModelData($modelData);
                foreach ($finderMakeData as $makeData) {
                    foreach ($finderModelData as $modelData) {
                        if (in_array($makeData['value_id'], array($modelData['parent_id']))) {
                            $finderValueId = $modelData['value_id'];
                        }
                    }
                }
                $logger->info("finderMakeData case 2" . print_r($finderMakeData, true));
                $logger->info("finderModelData case 2" . print_r($finderModelData, true));
                $logger->info("finderValueId case 2" . print_r($finderValueId, true));

            } elseif (count($paramData) == 1) {

                $makeData = $paramData[0];
                $finderMakeData = $this->getSeachFinderMakeData($makeData);
                foreach ($finderMakeData as $makeData) {
                    $finderValueId = $makeData['value_id'];
                }

                $logger->info("finderMakeData case 1" . print_r($finderMakeData, true));
                $logger->info("finderValueId case 1" . print_r($finderValueId, true));
            }

           // $array1['find'] = "M3";//$yearMakeModelParam . '-' . $finderValueId;
            $array1['find'] = $yearMakeModelParam . '-' . $finderValueId;
            /* EOF Anup Code for get Value Id */
            $logger->info("queryparamstring" . print_r($yearMakeModelParam . '-' . $finderValueId, true));
            $this->getRequest()->setParams($array1);
            $urlParam = $this->getRequest()->getParam('find');
        }

        //var_dump($urlParam);

        // XSS disabling
        $filter = ["<", ">"];
        $urlParam = str_replace($filter, "|", $urlParam);
        $urlParam = htmlspecialchars($urlParam);
        $logger->info("linn no : ".__LINE__);
        if ($urlParam) {
            $logger->info("linn no : ".__LINE__);
            $urlParam = $finder->parseUrlParam($urlParam);
            $logger->info("linn no : ".__LINE__);
            $current = $finder->getSavedValue('current',172);
            $logger->info("current : ".$current);
            $logger->info("linn no : ".__LINE__);

            if ($urlParam && ($current != $urlParam)) {
                // url has higher priority than session
                $logger->info("linn no : ".__LINE__);
                $dropdowns = $finder->getDropdownsByCurrent($urlParam);
                $logger->info("linn no : ".__LINE__);
                $finder->saveFilter($dropdowns, $this->getCurrentCategoryId(), [$this->getCurrentApplyUrl()]);
            }
        }
        $logger->info("linn no : ".__LINE__);
        $isUniversal = (bool)$this->configHelper->getConfigValue('advanced/universal');
        $isUniversalLast = (bool)$this->configHelper->getConfigValue('advanced/universal_last');
        $logger->info("linn no : ".__LINE__);

        /* if ($this->paramsExist()) {*/
        $logger->info("linn no : ".__LINE__);
        $finder->applyFilter($this->catalogLayer, $isUniversal, $isUniversalLast);
        $logger->info("linn no : ".__LINE__);
        /*}*/
        return $this;
    }

    /**
     * @return bool
     */
    private function paramsExist()
    {
        return strpos($this->_urlBuilder->getCurrentUrl(), 'find=') !== false;
    }

    /**
     * @param $url
     * @return string
     */
    private function formatUrl($url)
    {
        if ($this->_storeManager->getStore()->isCurrentlySecure()) {
            $url = str_replace("http://", "https://", $url);
        }

        return $this->urlEncoder->encode($url);
    }

    /**
     * @return int
     */
    public function getCurrentCategoryId()
    {
        return $this->catalogLayer->getCurrentCategory()->getId();
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        return $this->jsonEncoder->encode([
            'ajaxUrl' => $this->getAjaxUrl(),
            'isPartialSearch' => (int)$this->configHelper->getConfigValue('general/partial_search'),
            'autoSubmit' => (int)$this->configHelper->getConfigValue('advanced/auto_submit'),
            'isChosenEnable' => (int)$this->configHelper->getConfigValue('advanced/is_chosen_enable'),
            'containerId' => 'amfinder_' . $this->getFinder()->getId(),
            'loadingText' => __('Loading...')
        ]);
    }

    /**
     * @return array|string
     */
    private function getCurrentApplyUrl()
    {
        $currentUrl = $this->_urlBuilder->getCurrentUrl();
        $currentUrl = explode('?', $currentUrl);
        $currentUrl = array_shift($currentUrl);
        return $currentUrl;
    }

    /**
     * @return string
     */
    public function getCurrentApplyUrlEncoded()
    {
        $currentUrl = $this->getCurrentApplyUrl();
        return $this->urlEncoder->encode($currentUrl);
    }

    /**
     * @return float|string
     */
    public function getDropdownWidth()
    {
        $isMobile = isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'], 'mobi') !== false;
        $finder = $this->getFinder();

        if (!$isMobile) {
            $result = $finder->getTemplate() == self::HORIZONTAL
                ? floor(100 / count($finder->getDropdowns()) - self::SIZE_FOR_BUTTONS) : '';
        }

        return isset($result) ? $result : self::ALL_SIZE;
    }

    /**
     * @param $finder
     * @param $dropdown
     * @return string
     */
    public function getDropdownHtml($finder, $dropdown)
    {
        $dropdownHtml = $this->getLayout()->createBlock(\Amasty\Finder\Block\DropdownRenderer::class)
            ->setDropdown($dropdown)
            ->setFinder($finder)
            ->setParentDropdownId($this->getParentDropdownId())
            ->toHtml();

        $this->setParentDropdownId($dropdown->getId());

        return $dropdownHtml;
    }

    /**
     * @return bool
     */
    public function getHideClassName()
    {
        return $this->getFinder()->getDefaultCategory() && $this->getFinder()->isHideFinder();
    }

    /**
     * @return int
     */
    public function getParentDropdownId()
    {
        return $this->parentDropdownId;
    }

    /**
     * @param int $parentDropdownId
     */
    public function setParentDropdownId($parentDropdownId)
    {
        $this->parentDropdownId = $parentDropdownId;
    }
}
