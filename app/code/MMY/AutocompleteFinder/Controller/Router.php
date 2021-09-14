<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace MMY\AutocompleteFinder\Controller;

use Magento\Framework\Module\Manager;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Amasty\Finder\Model\ResourceModel\Value\CollectionFactory;
use Amasty\Finder\Model\ResourceModel\Dropdown\CollectionFactory as DropdownCollectionFactory;
use MMY\AutocompleteFinder\Helper\Data;
use Amasty\Finder\Model\Finder;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    private $actionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Url\Encoder
     */
    private $urlEncoder;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    private $catalogLayer;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var DropdownCollectionFactory
     */
    protected $dropdownCollectionFactory;

    /**
     * @var \Amasty\Finder\Api\FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var \Amasty\Finder\Helper\Config
     */
    private $configHelper;

    protected $finder;


    /**
     * Router constructor.
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Url\Encoder $urlEncoder
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CollectionFactory $collectionFactory
     * @param DropdownCollectionFactory $dropdownCollectionFactory
     * @param \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository
     * @param Data $helperData
     * @param \Amasty\Finder\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Url\Encoder $urlEncoder,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CollectionFactory $collectionFactory,
        DropdownCollectionFactory $dropdownCollectionFactory,
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository,
        Data $helperData,
        \Amasty\Finder\Helper\Config $configHelper

    )
    {
        $this->actionFactory = $actionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->coreRegistry = $registry;
        $this->urlBuilder = $urlBuilder;
        $this->urlEncoder = $urlEncoder;
        $this->catalogLayer = $layerResolver->get();
        $this->collectionFactory = $collectionFactory;
        $this->dropdownCollectionFactory = $dropdownCollectionFactory;
        $this->finderRepository = $finderRepository;
        $this->helperData = $helperData;
        $this->configHelper = $configHelper;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        if (strpos($identifier, 'exhaust-system') !== false) {
            $searchUrl = explode('-exhaust-system', $identifier);
            $urlData = explode('-',$searchUrl[0]);
            //   print_r($urlData);exit;
            /*  if ($url_key && strpos($url_key, '-') !== false) {
                  $this->helperData->log("url_key :".$url_key);
                  $mmy = explode("-", $url_key);

                  $this->helperData->log("year : ".$year.'==='.$model);


                  $request->setModuleName('amfinder')->setControllerName('index')->setActionName('index');
                  if (count($params)) {
                      $request->setParams($params);
                  }*/

            // $request->setModuleName('amfinder')->setControllerName('mmy')->setActionName('index');
            // return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
            /* $request->setModuleName('amfinder')->setControllerName('mmy')->setActionName('index');


             $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

             return $this->actionFactory->create(
                 'Magento\Framework\App\Action\Forward',
                 ['request' => $request]
             );*/

            //}

        }
    }


}
