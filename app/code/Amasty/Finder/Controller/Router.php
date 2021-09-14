<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Controller;
use Magento\Catalog\Api\ProductRepositoryInterface ;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Amasty\Finder\Model\ResourceModel\Value\CollectionFactory;
use Amasty\Finder\Model\ResourceModel\Dropdown\CollectionFactory as DropdownCollectionFactory;
use MMY\AutocompleteFinder\Helper\Data;
use Magento\Framework\App\Response\Http;
use mysql_xdevapi\Exception;

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
	/**
     * @var  \Magento\Framework\App\ResourceConnection
	*/
	private $_resourse;

    /**
     * @var Http
     */
    private $response;


    /** @var \Magento\Framework\Controller\Result\ForwardFactory */
    private $resultForwardFactory;

    protected $product;
    protected $finder;
    protected $connection;
    protected $_urlInterface;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Router constructor.
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Url\Encoder $urlEncoder
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
        CollectionFactory $collectionFactory,
        DropdownCollectionFactory $dropdownCollectionFactory,
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\UrlInterface $urlInterface,
        \Psr\Log\LoggerInterface $logger,
        Data $helperData,
        \Amasty\Finder\Helper\Config $configHelper,
        ProductRepositoryInterface $productCollectionFactory,
        Http $response,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->coreRegistry = $registry;
        $this->urlBuilder = $urlBuilder;
        $this->urlEncoder = $urlEncoder;
        $this->collectionFactory = $collectionFactory;
        $this->dropdownCollectionFactory = $dropdownCollectionFactory;
        $this->finderRepository = $finderRepository;
        $this->helperData = $helperData;
        $this->configHelper = $configHelper;
        $this->_resource = $resource;
        $this->_urlInterface = $urlInterface;
        $this->logger = $logger;
		$this->connection=$resource->getConnection();
        $this->product = $productCollectionFactory;
        $this->response = $response;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface
     * To match urls from table and route to the controller accordingly
     */
    public function match(RequestInterface $request)
    {

       /* $debugBackTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($debugBackTrace as $item) {
            echo @$item['class'].@$item['type'].@$item['function']."\n";
        }*/

        //echo " in MMY match ";
        //exit();
        //return null;

        $finderPageUrl = $this->scopeConfig->getValue('amfinder/general/custom_category', ScopeInterface::SCOPE_STORE)
            ?: 'amfinder';
		$oscUrls='';

        $identifier = trim($request->getPathInfo(), '/');

        //now check if this string conatins exhaust-system

         $oscUrls=str_replace('-exhaust-systems','',$identifier);
		  if(!empty($oscUrls)){

		  $gtableName = $this->_resource->getTableName('osc_urls');
        echo  $select = $this->connection->select()->from(
                $gtableName,
                ['Year','Make','Model','SubModel','Engine','Body']
            )->where(" url_key='". $oscUrls."'");

			$controllerName='';

            $rows = $this->connection->fetchRow($select);
			$finderController=false;

            $mmyFlag = 0;

			 if($rows){                                   // check for controller name
                    if(count($rows)>0){
                      if((!is_null($rows['Year']))) {
                          $mmyFlag = 1;
                          $controllerName .= 'y';
                      }
                      if(!is_null($rows['Make'])){
                          $mmyFlag = 1;
                       $controllerName .='m';
                      }
                      if(!is_null($rows['Model'])){
                          $mmyFlag = 1;
                          $controllerName .='m';
                      }

                      if(!is_null($rows['SubModel'])){
                          $mmyFlag = 1;
                          $controllerName .='s';
                      }

                        if(!is_null($rows['Body'])){
                            $mmyFlag = 1;

                        }

                    }
                 if($controllerName==='m'){
                     $mmyFlag = 1;
                     $controllerName='make';
                 }

                 $finderController=true;
			 }
            echo 'this is flag'.$mmyFlag;
        if($mmyFlag == 0){
            try{
                $gtableName = $this->_resource->getTableName('catalog_product_entity');

                $select = $this->connection->select()->from(
                    $gtableName,
                    ['sku']
                )->where("sku IN(?)",$oscUrls);

                $rows = $this->connection->fetchRow($select);
                if ($rows){
                    $data = $this->product->get($oscUrls);
                    $request->setParam('id',$data->getId());
                    $request->setModuleName('catalog')->setControllerName('product')->setActionName('view');
                    return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
                }else{

                    $request->setModuleName('amfinder')->setControllerName('redirectvehiclereq')->setActionName('index');
                    return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
                }

            }catch (Exception $e){
                $request->setModuleName('amfinder')->setControllerName('index')->setActionName('index');
                return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
            }


        }

        if($mmyFlag == 1){
            echo $controllerName;
            if(strpos($identifier, 'exhaust-systems') !== false && !empty($oscUrls) && $finderController) {
                $request->setParam('oscfinder',$oscUrls);
                $request->setModuleName('amfinder')->setControllerName($controllerName)->setActionName('index');
                return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
            }elseif($identifier == $finderPageUrl){
                $request->setModuleName('amfinder')->setControllerName('index')->setActionName('index');
                return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
            }
        }
    }
	}

    /**
     * @param $searchString
     * @return array
     * To get all available finders and finder values from database
     */

    protected function getFinderBySearch($searchString)
    {
        $finderValueData = [];
        $finderCollections = $this->collectionFactory
            ->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('name', trim($searchString))
            ->addFieldToFilter('parent_id', array('eq' => 0));
        $this->helperData->log("router value query1".$finderCollections->getSelect());
        foreach ($finderCollections as $collection) {
            $finderValueData[$collection->getValueId()] = $collection->getDropdownId();
        }

        $this->helperData->log("finderValueData data".print_r($finderValueData,1));
        return $finderValueData;
    }

    /**
     * @param $finderModelData
     * @return string
     * To get All finders dropdown by dropdownID from database
     */

    protected function getFinderByDropdownId($finderModelData)
    {
        $finderId = '';
        if(!empty($finderModelData)){
            $finderDropdownIds = $finderModelData;
            $this->helperData->log("rounter finderDropdownIds ".print_r($finderDropdownIds,1));
                if(isset($finderDropdownIds)) {
                    $dropdownCollection = $this->dropdownCollectionFactory->create();
                    $dropdownCollection->addFieldToSelect('finder_id');
                    $dropdownCollection->addFieldToFilter('dropdown_id', array('in' => $finderDropdownIds));
                    $this->helperData->log("rounter download query1 ".$dropdownCollection->getSelect());
                    foreach ($dropdownCollection as $collection) {
                        $finderId = $collection->getFinderId();
                        $this->helperData->log(" rounter all finderId".$finderId);
                    }
                }
        }
        $this->helperData->log(" rounter finderId".$finderId);
        return $finderId;
    }

}
