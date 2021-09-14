<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model;

use Amasty\Finder\Api\Data\FinderInterface;
use Amasty\Finder\Api\MapRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Finder extends \Magento\Framework\Model\AbstractModel implements FinderInterface
{
    const LAST_DROPDOWN = 'last';
    const CURRENT_DROPDOWN = 'current';
    const FILTER_CATEGORY_ID = 'filter_category_id';
    const APPLY_URL = 'apply_url';
    const URL_PARAM = 'url_param';
    const DROPDOWN_NAMES = 'dropdown_names';
    const TMP_IMAGE_DIRECTORY = '/amasty/finder/images_tmp';

    /** @var Session */
    private $session;

    /** @var \Magento\Framework\App\Response\RedirectInterface */
    private $redirect;

    /** @var \Magento\Framework\App\Response\Http */
    private $response;

    /** @var MapRepositoryInterface */
    private $mapRepository;

    /** @var \Amasty\Finder\Api\ValueRepositoryInterface */
    private $valueRepository;

    /**
     * @var \Amasty\Finder\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Amasty\Finder\Api\DropdownRepositoryInterface
     */
    private $dropdownRepository;

    /**
     * @var Import
     */
    private $importModel;

    /**
     * @var \Amasty\Finder\Api\FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    private $layer;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Finder\Model\Session $session,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Response\Http $response,
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository,
        \Amasty\Finder\Api\MapRepositoryInterface $mapRepository,
        \Amasty\Finder\Api\ValueRepositoryInterface $valueRepository,
        \Amasty\Finder\Helper\Config $configHelper,
        \Amasty\Finder\Model\Import $importModel,
        \Amasty\Finder\Api\DropdownRepositoryInterface $dropdownRepository,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->session = $session;
        $this->redirect = $redirect;
        $this->response = $response;
        $this->mapRepository = $mapRepository;
        $this->valueRepository = $valueRepository;
        $this->configHelper = $configHelper;
        $this->dropdownRepository = $dropdownRepository;
        $this->importModel = $importModel;
        $this->finderRepository = $finderRepository;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->layer = $layerResolver->get();
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(\Amasty\Finder\Model\ResourceModel\Finder::class);
        parent::_construct();
    }

    /**
     * @return array
     */
    public function getDropdowns()
    {
        return $this->dropdownRepository->getByFinderId($this->getId());
    }

    /**
     * @param $dropdowns
     * @param $categoryId
     * @param $applyUrls
     * @return bool
     */
    public function saveFilter($dropdowns, $categoryId, $applyUrls)
    {
        if (!$dropdowns) {
            return false;
        }

        if (!is_array($dropdowns)) {
            return false;
        }

        $safeValues = [];
        $dropdownId = 0;
        $dropdownNames = [];
        $current = 0;
        foreach ($this->getDropdowns() as $dropdown) {
            $dropdownId = $dropdown->getId();
            $dropdownNames[$dropdownId] = $dropdown->getName();
            $safeValues[$dropdownId] = isset($dropdowns[$dropdownId]) ? $dropdowns[$dropdownId] : 0;
            if (isset($dropdowns[$dropdownId]) && ($dropdowns[$dropdownId])) {
                $current = $dropdowns[$dropdownId];
            }
        }

        if ($dropdownId) {
            $safeValues[self::LAST_DROPDOWN] = $safeValues[$dropdownId];
            $safeValues[self::CURRENT_DROPDOWN] = $current;
        }

        $safeValues[self::FILTER_CATEGORY_ID] = $categoryId;
        $safeValues[self::APPLY_URL] = array_unique($applyUrls);
        $safeValues[self::URL_PARAM] = $this->createUrlParam($safeValues);
        $safeValues[self::DROPDOWN_NAMES] = $dropdownNames;
        if ($safeValues[self::URL_PARAM]) {
            $this->session->setFinderData($this->getId(), $safeValues);
        }

        return true;
    }

    /**
     * @return bool
     */
    public function resetFilter()
    {
        $this->session->reset($this->getId());
        return true;
    }

    /**
     * @param \Magento\Catalog\Model\Layer $layer
     * @param $isUniversal
     * @param $isUniversalLast
     * @return bool
     */
    public function applyFilter(\Magento\Catalog\Model\Layer $layer, $isUniversal, $isUniversalLast)
    {
        $id = $this->getSavedValue(self::CURRENT_DROPDOWN);
        if (!$id) {
            return false;
        }

        if (!$this->isAllowedInCategory($layer->getCurrentCategory()->getId())) {
            return false;
        }

        $finderId = $this->getId();

        $collection = $layer->getProductCollection();
        $cnt = $this->countEmptyDropdowns();

        $this->finderRepository->addConditionToProductCollection(
            $collection,
            $id,
            $cnt,
            $finderId,
            $isUniversal,
            $isUniversalLast
        );

        $isSingleProductRedirect = $this->configHelper->getConfigValue('advanced/redirect_single_product');

        $cloneCollection = clone $collection;
        if ($isSingleProductRedirect && $cloneCollection->count() == 1) {
            $product = $cloneCollection->getFirstItem();
            if ($this->session->getSingleProductCookieValue() !== $finderId . $product->getId()) {
                $url = $product->getProductUrl();
                $collection->clear();
                $this->session->setSingleProductCookie($finderId . $product->getId());
                $this->redirect->redirect($this->response, $url);
            }
        }

        return true;
    }

    /**
     * @param $dropdownId
     * @return int|bool
     */
    public function getSavedValue($dropdownId)
    {
        if ($dropdownId && !$this->isAllowedInCategory($this->layer->getCurrentCategory()->getId())) {
            return false;
        }

        $result = 0;
        $values = $this->session->getFinderData($this->getId());

        if (isset($values['url_param']) && $this->isParamsInUrl($values)) {
            return false;
        }

        if (is_array($values) && isset($values[$dropdownId]) && $values[$dropdownId]) {
            $result = $values[$dropdownId];
        }

        return $result;
    }

    /**
     * @param $values
     * @return bool
     */
    private function isParamsInUrl($values)
    {
        $finderParams = $this->request->getParam('find', null);

        return is_string($finderParams)
            ? strpos($finderParams, $values['url_param']) === false
            : $finderParams != $values['url_param'];
    }

    /**
     * @param $file
     * @return array
     */
    public function importUniversal($file)
    {
        return $this->finderRepository->importUniversal($this, $file);
    }

    /**
     * @param $file
     */
    public function importImages($file)
    {
        return $this->valueRepository->importImages($this, $file);
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteMapRow($id)
    {
        return $this->mapRepository->deleteById($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function isDeletable($id)
    {
        return $this->finderRepository->isDeletable($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function newSetterId($id)
    {
        return $id ? $this->mapRepository->getById($id)->getValueId() : false;
    }

    /**
     * @return int
     */
    private function countEmptyDropdowns()
    {
        $num = 0;

        // we assume the values are always exist.
        $values = $this->session->getFinderData($this->getId());
        foreach ($values as $key => $dropdown) {
            if (is_numeric($key) && !$dropdown) {
                $num++;
            }
        }

        return $num;
    }

    /**
     * @param $current
     * @return array
     */
    public function getDropdownsByCurrent($current)
    {
        $dropdowns = [];
        while ($current) {
            try {
                $valueModel = $this->valueRepository->getById($current);
                $dropdowns[$valueModel->getDropdownId()] = $current;
                $current = $valueModel->getParentId();
            } catch (NoSuchEntityException $e) {
                $current = false;
            }
        }

        return $dropdowns;
    }

    /**
     * @return null|string
     */
    public function getUrlParam()
    {
        $values = '';
        $allActiveFinders = $this->session->getAllFindersData();
        if (!$allActiveFinders) {
            return null;
        }

        foreach ($allActiveFinders as $value) {
            if (!is_array($value) || empty($value[self::URL_PARAM])) {
                return null;
            }

            $values .= !$values ? $value[self::URL_PARAM] : '&' . $value[self::URL_PARAM];
        }
        return $values;
    }

    /**
     * For current finder creates his description for URL
     *
     * @return string like year-make-model-number.html
     */
    private function createUrlParam($values)
    {
        $sep = $this->configHelper->getConfigValue('general/separator');
        $suffix = $this->configHelper->getConfigValue('general/suffix');

        $urlParam = '';

        foreach ($values as $key => $value) {
            if (self::CURRENT_DROPDOWN == $key) {
                $urlParam .= $value . $suffix;
                break;
            }

            if (!empty($value) && is_numeric($key)) {
                try {
                    $valueModel = $this->valueRepository->getById($value);
                    $urlParam .= strtolower(preg_replace('/[^\da-zA-Z]/', '-', $valueModel->getName())) . $sep;
                } catch (NoSuchEntityException $e) {
                    $urlParam = null;
                }

            }
        }
        if (empty($urlParam)) {
            $urlParam = null;
        }

        return $urlParam;
    }

    /**
     *  Get last `number` part from the year-make-model-number.html string
     *
     * @param string $param like year-make-model-number.html
     *
     * @return string like number
     */
    public function parseUrlParam($param)
    {
        $sep = $this->configHelper->getConfigValue('general/separator');
        $suffix = $this->configHelper->getConfigValue('general/suffix');

        $param = explode($sep, $param);
        $param = str_replace($suffix, '', $param[count($param) - 1]);

        return $param;
    }

    /**
     * @param $url
     * @param $name
     * @return string
     */
    public function removeGet($url, $name)
    {
        $url = str_replace("&amp;", "&", $url);
        list($routePart, $paramsPart) = array_pad(explode("?", $url), 2, "");
        // @codingStandardsIgnoreStart
        parse_str($paramsPart, $params);
        // @codingStandardsIgnoreEnd

        if ($params && isset($params[$name])) {
            $findParams = explode('&', $params[$name]);
            foreach ($findParams as $key => $item) {
                if (strpos($this->getUrlParam(), $item) === false) {
                    unset($findParams[$key]);
                }
            }

            if ($findParams) {
                $params[$name] = join('&', $findParams);
            } else {
                unset($params[$name]);
            }
        }

        $url = count($params) > 0 ? $routePart . "?" . http_build_query($params) : $routePart;

        return $url;
    }

    /**
     * @return int
     */
    private function getInitialCategoryId()
    {
        $value = $this->session->getFinderData($this->getId());

        return isset($value[self::FILTER_CATEGORY_ID]) ? $value[self::FILTER_CATEGORY_ID] : 0;
    }

    /**
     * @param $currentCategoryId
     * @return bool
     */
    private function isAllowedInCategory($currentCategoryId)
    {
        $res = $this->configHelper->getConfigValue('general/category_search');
        if (!$res) {
            return true;
        }

        if (!$this->getInitialCategoryId()
            || !empty($this->configHelper->getConfigValue('general/custom_category'))
        ) {
            return true;
        }

        return ($this->getInitialCategoryId() == $currentCategoryId);
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterDelete()
    {
        $this->importModel->afterDeleteFinder($this->getId());
        return parent::afterDelete();
    }

    /**
     * @return int
     */
    public function getFinderId()
    {
        return $this->_getData(FinderInterface::FINDER_ID);
    }

    /**
     * @param int $finderId
     * @return $this
     */
    public function setFinderId($finderId)
    {
        $this->setData(FinderInterface::FINDER_ID, $finderId);

        return $this;
    }

    /**
     * @return int
     */
    public function getCnt()
    {
        return $this->_getData(FinderInterface::CNT);
    }

    /**
     * @param int $cnt
     * @return $this
     */
    public function setCnt($cnt)
    {
        $this->setData(FinderInterface::CNT, $cnt);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_getData(FinderInterface::NAME);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->setData(FinderInterface::NAME, $name);

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->_getData(FinderInterface::TEMPLATE);
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->setData(FinderInterface::TEMPLATE, $template);

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->_getData(FinderInterface::META_TITLE);
    }

    /**
     * @param string $metaTitle
     * @return $this
     */
    public function setMetaTitle($metaTitle)
    {
        $this->setData(FinderInterface::META_TITLE, $metaTitle);

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescr()
    {
        return $this->_getData(FinderInterface::META_DESCR);
    }

    /**
     * @param string $metaDescr
     * @return $this
     */
    public function setMetaDescr($metaDescr)
    {
        $this->setData(FinderInterface::META_DESCR, $metaDescr);

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomUrl()
    {
        return $this->_getData(FinderInterface::CUSTOM_URL);
    }

    /**
     * @param string $customUrl
     * @return $this
     */
    public function setCustomUrl($customUrl)
    {
        $this->setData(FinderInterface::CUSTOM_URL, $customUrl);

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultCategory()
    {
        return $this->_getData(FinderInterface::DEFAULT_CATEGORY);
    }

    /**
     * @param $defaultCategory
     * @return $this
     */
    public function setDefaultCategory($defaultCategory)
    {
        $this->setData(FinderInterface::DEFAULT_CATEGORY, $defaultCategory);

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setFinderIsVisible($value)
    {
        $this->setData(FinderInterface::HIDE_FINDER, $value);

        return $this;
    }

    /**
     * @return bool
     */
    public function getFinderIsVisible()
    {
        return !$this->_getData(FinderInterface::HIDE_FINDER);
    }

    /**
     * @return bool
     */
    public function isHideFinder()
    {
        $isDefaultCategory = strpos($this->request->getFullActionName(), 'cms_') === false
            && $this->request->getFullActionName() !== 'catalog_product_view'
            && $this->request->getFullActionName() !== 'catalogsearch_result_index'
            && $this->storeManager->getStore()->getRootCategoryId() == $this->layer->getCurrentCategory()->getId();

        return $this->getFinderIsVisible() && $isDefaultCategory;
    }

    /**
     * @param $hideFinder
     * @return $this
     */
    public function setHideFinder($hideFinder)
    {
        $this->setData(FinderInterface::HIDE_FINDER, $hideFinder);

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->_getData(FinderInterface::POSITION);
    }

    /**
     * @param $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->setData(FinderInterface::POSITION, $position);

        return $this;
    }

    /**
     * @return string
     */
    public function getFinderXmlCode()
    {
        $position = $this->getPosition() ?: 'content.top';

        return '<referenceContainer name="' . $position . '">
                         <block class="Amasty\Finder\Block\Form" name="amasty.finder.' . $this->getId() . '">
                             <arguments>
                                 <argument name="id" xsi:type="string">' . $this->getId() . '</argument>
                                 <argument name="location" xsi:type="string">xml</argument>
                             </arguments>
                         </block>
                     </referenceContainer>';
    }
}
