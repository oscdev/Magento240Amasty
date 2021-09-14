<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Block\ProductFinder;

use Amasty\Finder\Model\ResourceModel\Value\CollectionFactory;
use Magento\Framework\App\RequestInterface;
class Profinder  extends \Magento\Framework\View\Element\Template
{
    private $layerResolver;
    private $catalogLayer;
    private $_resource;
    private $connection;
    private $request;
    private $redirect;
    private $searchCriteriaBuilderFactory;
    private $response;
    private $configHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Amasty\Finder\Model\Finder\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        RequestInterface $request,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Response\Http $response,
        \Amasty\Finder\Helper\Config $configHelper

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
        )->where(" url_key='". $oscUrls."'");
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
        $isSingleProductRedirect = $this->configHelper->getConfigValue('advanced/redirect_single_product');
        $cloneCollection = clone $collection;
        if ($isSingleProductRedirect && $cloneCollection->count() == 1) {
            $product = $cloneCollection->getFirstItem();

            $url = $product->getProductUrl();
            $collection->clear();
            $this->redirect->redirect($this->response, $url);

        }

        $this->searchCriteriaBuilderFactory->get()
            ->addCollectionFilter($collection, 'sku', $productSkus);

        return true;

    }

    public function getAllFinders()
    {

        $gtableName = $this->_resource->getTableName('osc_urls');
        $oscUrls=$this->request->getParam('oscfinder');
        $select = $this->connection->select()->from(
            $gtableName,
            ['SubModel','Engine','Body']
        )->where(" url_key='". $oscUrls."'");
        $rows = $this->connection->fetchAll($select);
        $finders = [];
        foreach($rows as $index=>$value)
        {
            foreach ($value as $findername => $variable){
                $finders[$findername][] = $variable;
            }

        }
        return $finders;

    }

}
