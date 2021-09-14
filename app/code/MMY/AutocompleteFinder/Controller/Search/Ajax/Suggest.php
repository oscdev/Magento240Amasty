<?php
/**
 * MMY_AutocompleteFinder Module Registration
 *
 * @category    AutocompleteFinder
 * @package     AutocompleteFinder
 * @author      MMY
 *
 */
namespace MMY\AutocompleteFinder\Controller\Search\Ajax;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Framework\Controller\ResultFactory;
use Amasty\Finder\Model\ResourceModel\Value\CollectionFactory;
use MMY\AutocompleteFinder\Helper\Data;
use Amasty\Finder\Model\ResourceModel\Dropdown\CollectionFactory as DropdownCollectionFactory;
use Amasty\Finder\Model\ResourceModel\Map\CollectionFactory as MapCollectionFactory;


class Suggest extends \Magento\Search\Controller\Ajax\Suggest
{
    /**
     * @var  \Magento\Search\Model\AutocompleteInterface
     */
    private $autocomplete;

    /**
     * @var DropdownCollectionFactory
     */
    protected $dropdownCollectionFactory;

    /**
     * @var MapCollectionFactory
     */
    protected $mapCollectionFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;



    /**
     * @var Data
     */
    protected $helperData;
    private $_resource;
    private $connection;
    CONST YEAR_SEVERITY=1;
    CONST MAKE_SEVERITY=2;
    CONST MODEL_SEVERITY=3;
    CONST SUBMODEL_SEVERITY=4;
    CONST ENGINE_SEVERITY=5;
    CONST BODY_SEVERITY=6;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Search\Model\AutocompleteInterface $autocomplete
     */
    public function __construct(
        Context $context,
        AutocompleteInterface $autocomplete,
        CollectionFactory $collectionFactory,
        DropdownCollectionFactory $dropdownCollectionFactory,
        MapCollectionFactory $mapCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,

        Data $helperData

    ) {
        $this->autocomplete = $autocomplete;
        $this->collectionFactory = $collectionFactory;
        $this->mapCollectionFactory = $mapCollectionFactory;
        $this->dropdownCollectionFactory = $dropdownCollectionFactory;
        $this->helperData = $helperData;

        parent::__construct($context,$autocomplete);
        $this->_resource=$resource;
        $this->connection=$resource->getConnection();
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * To get all combinations in search results when input suggestion
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('q', false)) {

            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }
        $querySearchText = $this->getRequest()->getParam('q', false);

        if($this->helperData->getModuleIsEnabled()) {
            $gtableName = $this->_resource->getTableName('search_patterns');
            $select = $this->connection->select()->from(
                $gtableName,
                ['search_key','match_finder']
            )->where(" match_pattern='".$querySearchText."'");


            $result=$this->connection->fetchRow($select);

            if(!$result){

                return $this->getSuggestions($querySearchText);

            }
            $severity=$result['search_key'];
            $matchedFider=$result['match_finder'];

            if($result){
                $finders=explode(' ',$matchedFider);
                //$generalSearch=
                switch($severity){
                    case 'Y':
                        $position=self::YEAR_SEVERITY;
                        $parms = ['Make','Model','SubModel','Engine','value_id'];
                        $query="Year='".$matchedFider."'";
                        break;
                    case 'YM':
                        $position=self::MAKE_SEVERITY;
                        $parms = ['Model','SubModel','Engine','Body','value_id'];
                        $query="Year='".$finders[0]."' and Make='". $finders[1]."'";
                        break;
                    case 'MMO':
                        $position=self::MODEL_SEVERITY;
                        $parms = ['Make','Model','SubModel','Engine','Body','value_id'];
                        $query="Make='".$finders[0]."' and Model='". $finders[1]."'";
                        break;
                    case 'MO':
                        $position=self::MODEL_SEVERITY;
                        $parms = ['Make','Model','SubModel','Engine','Body','value_id'];
                        $query="Model ='". $matchedFider."'";
                        break;
                    case 'M':
                        $position=self::MAKE_SEVERITY;
                        $parms = ['Model','SubModel','Engine','Body','value_id'];
                        $query="Make ='". $matchedFider."'";
                        break;
                    case 'S':

                        break;
                    case 'SE':

                        break;

                }
                #####################################################

                $gtableName = $this->_resource->getTableName('osc_urls');
                $select = $this->connection->select()->from(
                    $gtableName,
                    $parms
                )->where($query)
                    ->group('value_id')
                    ->limit(15);

                $rows = $this->connection->fetchAll($select);
                $value_id=[];
                $name=[];

                foreach($rows as $data){
                    $value_id[]=$data['value_id'];

                    switch($position){
                        case 1: ////For Year
                            $finderName=$matchedFider.' '.$data['Make'].' '.$data['Model'];
                            if($data['SubModel']){
                                $finderName .= ' '.$data['SubModel'];
                            }
                            if($data['Engine'])
                                $finderName .= ' '.$data['Engine'];

                            $name[]=$finderName;
                            break;
                        case 2: //For Make
                            $name[]=$matchedFider;
                            $name[]=$matchedFider.' '.$data['Model'];
                            $finderName=$matchedFider.' '.$data['Model'];
                            if($data['SubModel']){
                                $finderName .= ' '.$data['SubModel'];
                            }
                            if($data['Engine'])
                                $finderName .= ' '.$data['Engine'];
                            $name[]=$finderName;
                            break;
                        case 3://For Model
                            $finderName=$data['Make'].' '.$data['Model'];
                            if($data['SubModel']){
                                $finderName .= ' '.$data['SubModel'];
                            }
                            if($data['Engine'])
                                $finderName .= ' '.$data['Engine'];

                            $name[]=$finderName;
                            // $name[]=$data['Make'].' '.$data['Model'].' '.$data['SubModel'].' '.$data['Engine'];
                            break;

                    }

                }
                $gtableName = $this->_resource->getTableName('amasty_finder_map');
                $select = $this->connection->select()->from(
                    $gtableName,
                    ['count(sku) as total']
                )->where('value_id IN(?)', $value_id)
                    ->group('value_id');

                $rows = $this->connection->fetchAll($select);
                $totalProducts=[];
                foreach($rows as $productcount){

                    $totalProducts[]=$productcount['total'];

                }
                $forAll=array_sum($totalProducts);

                $searchText=array_unique($name,SORT_STRING);

                $responseData=[];
                $i=0;
                foreach($searchText as $index=>$suggestData){

                    $totalSearch=count($searchText);

                    $numProducts='0';

                    if($i<count($totalProducts)){
                        $numProducts=$totalProducts[$i];
                        $i++;
                    }




                    $responseData[] = array(
                        'title' =>$suggestData,
                        'num_results' =>$numProducts,
                    );
                }
            }
            else{

                $responseData[] = array(
                    'title' =>"No results found",
                    'num_results' =>'',
                );
            }
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($responseData);
            return $resultJson;
        }
    }

    public function getSuggestions($searchText){

        $gtableName = $this->_resource->getTableName('osc_urls');
        $finders=str_replace(' ','-',$searchText);
        if(is_array($finders)){


        }
        $select = $this->connection->select()->from(
            $gtableName,
            ['Make','Model','SubModel','Engine','Body','value_id']
        )->where(
            "url_key like '%".$finders."%'"
        )
            ->group('value_id')
        ;

        $rows = $this->connection->fetchAll($select);
        $value_id=[];
        $name=[];
        if($rows){
            foreach($rows as $data){
                $value_id[]=$data['value_id'];
                $name[]=$data['Make'].' '.$data['Model'].' '.$data['SubModel'].' '.$data['Engine'].' '.$data['Body'];

            }
            $gtableName = $this->_resource->getTableName('amasty_finder_map');
            $select = $this->connection->select()->from(
                $gtableName,
                ['count(sku) as total']
            )->where('value_id IN(?)', $value_id)
                ->group('value_id');

            $rows = $this->connection->fetchAll($select);
            $totalProducts=[];
            foreach($rows as $productcount){

                $totalProducts[]=$productcount['total'];

            }
            $forAll=array_sum($totalProducts);

            $searchText=array_unique($name,SORT_STRING);

            $responseData=[];
            $i=0;
            foreach($searchText as $index=>$suggestData){

                $totalSearch=count($searchText);

                $numProducts='0';

                if($i<count($totalProducts)){
                    $numProducts=$totalProducts[$i];
                    $i++;
                }




                $responseData[] = array(
                    'title' =>$suggestData,
                    'num_results' =>$numProducts,
                );
            }
        }
        else{

            $responseData[] = array(
                'title' =>"No results found",
                'num_results' =>'',
            );
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
        return $resultJson;

    }


}
