<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Block\Ymm;

use Amasty\Finder\Model\ResourceModel\Value\CollectionFactory;
use Magento\Framework\App\RequestInterface;
class Yearmakemodellist  extends \Magento\Framework\View\Element\Template
{
    private $layerResolver;
    private $catalogLayer;
    private $_resource;
    private $connection;
    private $request;
    private $redirect;
    private $searchCriteriaBuilderFactory;
    private $response;
    /**
     * @var \Amasty\Finder\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_json;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Amasty\Finder\Model\Finder\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        RequestInterface $request,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Response\Http $response,
        \Amasty\Finder\Helper\Config $configHelper,
        \Magento\Framework\Serialize\Serializer\Json $json

    ) {
        $this->layerResolver=$layerResolver;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->catalogLayer = $layerResolver->get();
        $this->_resource=$resource;
        $this->connection=$resource->getConnection();
        $this->request = $request;
        parent::__construct($context);
        $this->redirect = $redirect;
        $this->response = $response;
        $this->configHelper = $configHelper;
        $this->_json = $json;
        $this->applyymm();
        $this->getAllFinders();
    }

    function applyymm(){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/apply.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->applyFinders($this->catalogLayer);
    }

    private function applyFinders(\Magento\Catalog\Model\Layer $layer){
        $gtableName = $this->_resource->getTableName('osc_urls');
        $oscUrls=$this->request->getParam('oscfinder');
        $select = $this->connection->select()->from(
            $gtableName,
            ['value_id']
        )->where(" url_key='". $oscUrls."'")
            ->group('value_id');

        $ids=$this->connection->fetchRow($select);

        $collection = $layer->getProductCollection();
        $gtableName = $this->_resource->getTableName('amasty_finder_map');
        $select = $this->connection->select()->from(
            $gtableName,
            ['sku']
        )->where("value_id IN(?)", $ids['value_id']);

        $rows = $this->connection->fetchAll($select);

        foreach($rows as $sku){
            $productSkus[]=$sku['sku'];

        }
        $this->searchCriteriaBuilderFactory->get()
            ->addCollectionFilter($collection, 'sku', $productSkus);

        $isSingleProductRedirect = $this->configHelper->getConfigValue('advanced/redirect_single_product');

        $cloneCollection = clone $collection;

        if ($isSingleProductRedirect && $cloneCollection->count() === 1) {
            $product = $cloneCollection->getFirstItem();

            $url = $product->getProductUrl();
            $collection->clear();
            $this->redirect->redirect($this->response, $url);

        }



        return true;

    }
    public function getSelection(){

        return $this->request->getParam('oscfinder');

    }
    public function getAllFinders()
    {
        $gtableName = $this->_resource->getTableName('osc_urls');
        $oscUrls=$this->request->getParam('oscfinder');
        $finders = [];
        $lastElement=false;
        $lastFinder=str_replace('-exhaust-systems','',$oscUrls);
        $engineBody=explode('-',$lastFinder);
        if(is_array($engineBody)){

            $last=array_pop($engineBody);
        }

        $selectEngine = $this->connection->select()->from(
            $gtableName,
            ['Engine']
        )->where("Engine= '".$last."'");
        $isExist=$this->connection->fetchRow($selectEngine);
        if(!empty($isExist['Engine'])){
            $lastElement=true;
        }
        $selectBody = $this->connection->select()->from(
            $gtableName,
            ['Body']
        )->where("Body= '".$last."'");
        $isExist=$this->connection->fetchRow($selectBody);
        if(!empty($isExist['Body'])){
            $lastElement=true;
        }
        if(!$lastElement){
            $select = $this->connection->select()->from(
                $gtableName,
                ['SubModel','Engine','Body']
            )->where("url_key like '%".$oscUrls."%'");

            $rows = $this->connection->fetchAll($select);

            foreach($rows as $index=>$value)
            {
                if(count($value)>0) {
                    foreach ($value as $findername => $variable) {
                        if ($variable)
                            $finders[$findername][] = $variable;
                    }
                }

            }
            if(isset($finders['SubModel']))
                $finders['SubModel']=array_unique($finders['SubModel'],SORT_STRING);
            if(isset($finders['Engine']))
                $finders['Engine']=array_unique($finders['Engine'],SORT_STRING);
            if(isset($finders['Body']))
                $finders['Body']=array_unique($finders['Body'],SORT_STRING);
        }
        return $finders;
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        $isCurrentlySecure = (bool)$this->_storeManager->getStore()->isCurrentlySecure();
        $secure = $isCurrentlySecure ? true : false;
        $url = $this->getUrl('amfinder/mm/ajaxoptions', ['_secure' => $secure]);
        return $url;
    }

    public function getUrlInfo(){
        $urls = false ;
        if(!empty($this->request->getParam('oscfinder'))) {
            $urls = $this->request->getParam('oscfinder');
        }
        return $urls;
    }

}


/* if ($finders){
     $_keyErrorMsg = '';
     $finders->representJson(
         $this->jsonHelper->jsonEncode(
             ['error' => true, 'message' => $_keyErrorMsg]
         )
     );
 }
 print_r($finders);
 exit();*/
