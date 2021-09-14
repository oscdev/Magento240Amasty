<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Block\Make;

use Amasty\Finder\Helper\Config;
use Amasty\Finder\Model\Finder\SearchCriteriaBuilderFactory;
use Amasty\Finder\Model\ResourceModel\Value\CollectionFactory;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\View\Element\Template\Context;
use Zend\Log\Writer\Stream;

class Makelist  extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var
     */
    private $catalogLayer;

    /**
     * @var ResourceConnection
     */
    private $_resource;

    /**
     * @var
     */
    private $connection;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var Http
     */
    private $response;

    /**
     * @var Config
     */
    private $configHelper;

    public function __construct(
        Context $context,
        Resolver $layerResolver,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        ResourceConnection $resource,
        RequestInterface $request,
        RedirectInterface $redirect,
        Http $response,
        Config $configHelper

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
        $this->applyMm();
        $this->getAllFinders();
    }

    function applyMm(){
        $writer = new Stream(BP . '/var/log/apply.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->applyFindersProductCollection($this->catalogLayer);
    }

    /**
     * @param \Magento\Catalog\Model\Layer $layer
     * @return bool
     * To get all finder product collection
     */

    private function applyFindersProductCollection(\Magento\Catalog\Model\Layer $layer){
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

        // for single product

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

    /**
     * @return array
     * To get finder dropdowns according to suggestion search
     */

    public function getAllFinders()
    {
        $gtableName = $this->_resource->getTableName('osc_urls');
        $oscUrls=$this->request->getParam('oscfinder');


        $select = $this->connection->select()->from(
            $gtableName,
            ['Year','Model']
        )->where("url_key like '%".$oscUrls."%'");


        $rows = $this->connection->fetchAll($select);
        $finders = [];
        foreach($rows as $index=>$value)
        {
            if(count($value)>0){

                foreach ($value as $findername => $variable){
                    if($variable)                             // check if dropdown value in available or not empty
                        $finders[$findername][] = $variable;
                }
			}
        }
		if(isset($finders['Year']))
        $finders['Year']=array_unique($finders['Year']);
        if(isset($finders['Model']))
            $finders['Model']=array_unique($finders['Model'],SORT_STRING);
        if(isset($finders['SubModel']))
            $finders['SubModel']=array_unique($finders['SubModel'],SORT_STRING);
        if(isset($finders['Engine']))
            $finders['Engine']=array_unique($finders['Engine'],SORT_STRING);
        if(isset($finders['Body']))
            $finders['Body']=array_unique($finders['Body'],SORT_STRING);

        return $finders;
    }

    /**
     * @return string
     * ajaxURl to call from template
     */

    public function getAjaxUrl()
    {
        $isCurrentlySecure = (bool)$this->_storeManager->getStore()->isCurrentlySecure();
        $secure = $isCurrentlySecure ? true : false;
        $url = $this->getUrl('amfinder/mm/ajaxoptions', ['_secure' => $secure]);   // common ajaxOption file for all forms.
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
