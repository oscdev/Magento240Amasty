<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Controller\Mm;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\RequestInterface;
use Zend\Log\Writer\Stream;

class AjaxOptions extends \Magento\Framework\App\Action\Action
{
    /**
     * @var DropdownRepositoryInterface
     */
    private $dropdownRepository;

    /**
     * @var \Magento\Framework\View\Layout
     */
    private $layout;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    private $request;
    private $redirect;
    private $response;

    const YEAR_SEVERITY=0;
    const MAKE_SEVERITY=1;
    const MODEL_SEVERITY=2;
    const SUBMODEL_SEVERITY=3;
    const ENGINE_SEVERITY=4;
    const BODY_SEVERITY=5;
    const ALL=['Year','Make','Model','SubModel','Engine','Body'];

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Finder\Api\DropdownRepositoryInterface $dropdownRepository,
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Response\Http $response,
        RequestInterface $request,
        ResourceConnection $resource
    ) {
        $this->dropdownRepository = $dropdownRepository;
        $this->layout = $layout;
        parent::__construct($context);
        $this->_resource=$resource;
        $this->request=$request;
        $this->response=$response;
        $this->redirect=$redirect;
        $this->storeManager = $storeManager;
        $this->connection=$resource->getConnection();
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     * To handle and get form dropdown values and redirect to product page on last selection
     */
    public function execute()
    {
        $isLast=false;
        $url_key = $this->getRequest()->getParam('urls', false);

        $isLast=$this->getRequest()->getParam('position');

        $nextFinder='';
        if($isLast==='false'){                                      // check if dropdown is last

          $nextFinder=$this->getRequest()->getParam('nextFinder');
        }


        $currentFinder = $this->getRequest()->getParam('current_finder'); // check and get finder dropdown currently enable

		$writer = new Stream(BP . '/var/log/apply.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('$nextFinder=='.$nextFinder);
        $logger->info('$current_finder=='.$currentFinder);

       $finderValue=$this->getRequest()->getParam($currentFinder);

        $condition='';
        $response = [];
        $response['redirect_url']='';
        $redirectUrl=[];
        $finders=[];
        $finders=array_keys($this->getRequest()->getParams());
      #######################################################
        // to get inputs from base url and also check is dropdown last

       $baseUrl=$this->request->getDistroBaseUrl();
        if($isLast==='true'){
            $finderParam=[];
            foreach($finders as $index=>$find){
                if(in_array($find,self::ALL)){
                    //$finderParam[]=array_search($find,self::ALL);
                    $finderParam[]=$find;
                }

            }

           /* $exculdeFinder=[];
            for($k=0;$k<count(self::ALL);$k++){
                if(!in_array(self::ALL[$k],$finderParam)){

                    $exculdeFinder[]=self::ALL[$k];
                }
            }
             $logger->info(print_R($finderParam,true));
            if(count($exculdeFinder)>0){
                $avilableFinder=implode('-',$exculdeFinder);
            }*/
			$avilableFinder='';

            //  Get available finders aas per input suggestion
			$avilableFinder=implode('-',$finderParam);
		     $logger->info('$avilableFinder=='.$avilableFinder);

             // Handle cases for dropdown according to input combination and redirect to product page

            switch ($avilableFinder) {

                case 'Year-Model':
                    for ($ym = 0; $ym < count(self::ALL) ; $ym++) {
                        if (in_array(self::ALL[$ym], $finderParam)) {
                            $redirectUrl[self::ALL[$ym]] = $this->getRequest()->getParam(self::ALL[$ym]);
                            if (!isset($redirectUrl[$avilableFinder])) {
                                $redirectUrl[$avilableFinder] = $url_key;
                            }
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

               case 'SubModel-Body':
                    for ($sb = 0; $sb < count(self::ALL) ; $sb++) {

                        if (in_array(self::ALL[$sb], $finderParam)) {
                            if (!isset($redirectUrl[$avilableFinder])) {
                                $redirectUrl[$avilableFinder] = $url_key;
                            }
                            $redirectUrl[self::ALL[$sb]] = $this->getRequest()->getParam(self::ALL[$sb]);
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

                case 'Year':
                    for ($y = 0; $y < count(self::ALL) ; $y++) {
					  if (in_array(self::ALL[$y], $finderParam)) {
                            $redirectUrl[self::ALL[$y]] = $this->getRequest()->getParam(self::ALL[$y]);
                        }
				    if (!isset($redirectUrl[$url_key])) {
                                $redirectUrl[$url_key] = $url_key;
                            }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

                 case 'SubModel-Engine':
                    for ($se = 0; $se < count(self::ALL) ; $se++) {

					  if (in_array(self::ALL[$se], $finderParam)) {
                            $redirectUrl[self::ALL[$se]] = $this->getRequest()->getParam(self::ALL[$se]);
                          if (!isset($redirectUrl[$url_key])) {
                              $redirectUrl[$url_key] = $url_key;
                          }
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;
					case 'Model':
                    for ($m = 0; $m < count(self::ALL) ; $m++) {
					 if (!isset($redirectUrl[$url_key])) {
                                $redirectUrl[$url_key] = $url_key;
                            }
					  if (in_array(self::ALL[$m], $finderParam)) {
                            $redirectUrl[self::ALL[$m]] = $this->getRequest()->getParam(self::ALL[$m]);
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

					case 'Body':
                    for ($b = 0; $b < count(self::ALL) ; $b++) {

					  if (in_array(self::ALL[$b], $finderParam)) {

                          if (!isset($redirectUrl[$url_key])) {
                              $redirectUrl[$url_key] = $url_key;
                          }
                          $redirectUrl[self::ALL[$b]] = $this->getRequest()->getParam(self::ALL[$b]);
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

					case 'Engine':
                    for ($e = 0; $e < count(self::ALL) ; $e++) {
					 if (!isset($redirectUrl[$url_key])) {
                                $redirectUrl[$url_key] = $url_key;
                            }
					  if (in_array(self::ALL[$e], $finderParam)) {
                            $redirectUrl[self::ALL[$e]] = $this->getRequest()->getParam(self::ALL[$e]);
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

					case 'Engine-Body':
                    for ($eb = 0; $eb < count(self::ALL) ; $eb++) {
					 if (!isset($redirectUrl[$url_key])) {
                                $redirectUrl[$url_key] = $url_key;
                            }
					  if (in_array(self::ALL[$eb], $finderParam)) {
                            $redirectUrl[self::ALL[$eb]] = $this->getRequest()->getParam(self::ALL[$eb]);
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;
                   case 'SubModel':
                    for ($s = 0; $s < count(self::ALL) ; $s++) {
					 if (!isset($redirectUrl[$url_key])) {
                                $redirectUrl[$url_key] = $url_key;
                            }
					  if (in_array(self::ALL[$s], $finderParam)) {
                            $redirectUrl[self::ALL[$s]] = $this->getRequest()->getParam(self::ALL[$s]);
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

                case 'SubModel-Engine-Body':
                    for ($seb = 0; $seb < count(self::ALL) ; $seb++) {
                        if (!isset($redirectUrl[$url_key])) {
                            $redirectUrl[$url_key] = $url_key;
                        }
                        if (in_array(self::ALL[$seb], $finderParam)) {
                            $redirectUrl[self::ALL[$seb]] = $this->getRequest()->getParam(self::ALL[$seb]);
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;
					############################################

                case 'Make-Model-SubModel':
                    for ($mms = 0; $mms < count(self::ALL) ; $mms++) {
                        if (in_array(self::ALL[$mms], $finderParam)) {
                            $redirectUrl[self::ALL[$mms]] = $this->getRequest()->getParam(self::ALL[$mms]);
                            if (!isset($redirectUrl[$avilableFinder])) {
                                $redirectUrl[$avilableFinder] = $url_key;
                            }
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

                case 'Make-Model-SubModel-Engine':
                    for ($mmse = 0; $mmse < count(self::ALL) ; $mmse++) {
                        if (in_array(self::ALL[$mmse], $finderParam)) {
                            $redirectUrl[self::ALL[$mmse]] = $this->getRequest()->getParam(self::ALL[$mmse]);
                            if (!isset($redirectUrl[$avilableFinder])) {
                                $redirectUrl[$avilableFinder] = $url_key;
                            }
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

                case 'Make':
                    for ($m = 0; $m < count(self::ALL); $m++) {
                        if (in_array(self::ALL[$m], $finderParam)) {
                            $redirectUrl[self::ALL[$m]] = $this->getRequest()->getParam(self::ALL[$m]);
                            if (!isset($redirectUrl[$avilableFinder])) {
                                $redirectUrl[$avilableFinder] = $url_key;
                            }
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

                case 'Year-Make':
                    if (!isset($redirectUrl[$avilableFinder])) {
                        $redirectUrl[$avilableFinder] = $url_key;
                    }

                    for ($ym = 0; $ym < count(self::ALL); $ym++) {
                        if (in_array(self::ALL[$ym], $finderParam)) {
                            $redirectUrl[self::ALL[$ym]] = $this->getRequest()->getParam(self::ALL[$ym]);

                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

                case 'Year-Make-Model':
                    if (!isset($redirectUrl[$avilableFinder])) {
                        $redirectUrl[$avilableFinder] = $url_key;
                    }
                    for ($ymm = 0; $ymm < count(self::ALL) ; $ymm++) {
                        if (in_array(self::ALL[$ymm], $finderParam)) {
                            $redirectUrl[self::ALL[$ymm]] = $this->getRequest()->getParam(self::ALL[$ymm]);
                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

                case 'Year-Make-Model-SubModel':
                    if (!isset($redirectUrl[$avilableFinder])) {
                        $redirectUrl[$avilableFinder] = $url_key;
                    }
                    for ($ymms = 0; $ymms < count(self::ALL); $ymms++) {
                        if (in_array(self::ALL[$ymms], $finderParam)) {
                            $redirectUrl[self::ALL[$ymms]] = $this->getRequest()->getParam(self::ALL[$ymms]);

                        }
                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;
                   case 'Make-Engine':

                    for ($ymms = 0; $ymms < count(self::ALL); $ymms++) {
                        if (in_array(self::ALL[$ymms], $finderParam)) {
                            $redirectUrl[self::ALL[$ymms]] = $this->getRequest()->getParam(self::ALL[$ymms]);
                            if (!isset($redirectUrl[$avilableFinder])) {
                        $redirectUrl[$url_key] = $url_key;
                    }
                        }

                    }
                    $redirectUrl['exhaust-systems'] = 'exhaust-systems';
                    break;

            }

            $response['redirect_url']=$baseUrl.implode('-',$redirectUrl);
            return ($this->getResponse()->setBody(json_encode($response)));
        }

   $logger->info(print_R($response,true));

        // create condition when get dropdown value for other dropdowns
        foreach($finders as $index=>$find){
            if(in_array($find,self::ALL)){
                $finderValue=$this->getRequest()->getParam($find);
				if($finderValue)
                $condition .=' And '.$find."='".$finderValue."'";
            }

        }

        // Get next dropdown values according to current enable dropdown as search condition (filter)

      $response['next']='';
        $gtableName = $this->_resource->getTableName('osc_urls');
        $oscUrls=$this->request->getParam('oscfinder');
      $select = $this->connection->select()->from(
            $gtableName,
            [$nextFinder]
        )->where(" url_key like'%". $url_key."%'".$condition)
            ->group($nextFinder);

        $data=$this->connection->fetchAll($select);

		 $logger->info('count=='.count($data));


         // make url and redirect to product page after submitting all dropdown values

       $redirect=true;
      foreach($data as $options){
          $logger->info(__line__);
            if($options[$nextFinder]){
                $response['option'][]='<option value="'.$options[$nextFinder]. '">'.$options[$nextFinder].'</option>';
				$redirect=false;
			}

        }
        	if($redirect){
			$redirectUrl[$url_key] = $url_key;
			$redirectUrl[$finderValue] = $finderValue;

			$redirectUrl['exhaust-systems'] = 'exhaust-systems';

			  $response['redirect_url']=$baseUrl.implode('-',$redirectUrl);


			}
        return $this->getResponse()->setBody(json_encode($response));
    }

    /**
     * @param $dropdown
     * @param int $parentValueId
     * @return array
     */
    private function getFinders($dropdown, $parentValueId)
    {

        return $options;
    }
}
