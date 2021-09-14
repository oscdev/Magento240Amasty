<?php
/**
 * MMY_AutocompleteFinder Module Registration
 *
 * @category    AutocompleteFinder
 * @package     AutocompleteFinder
 * @author      MMY
 *
 */
namespace MMY\AutocompleteFinder\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;
use Amasty\Finder\Model\ResourceModel\Value\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    protected $querySearchText;

    protected $resultFactory;

    protected $response;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    protected $_urlInterface;
    /**
     * module enable
     */
    const XML_PATH_IS_ENABLE = 'mmy_finder/general/is_enable';
    const XML_PATH_IS_DEBUG_ENABLE = 'mmy_finder/general/debug_enable';

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        CollectionFactory $collectionFactory,
        ResultFactory $resultFactory,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\UrlInterface $urlInterface,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->collectionFactory = $collectionFactory;
        $this->resultFactory = $resultFactory;
        $this->response = $response;
        $this->_urlInterface = $urlInterface;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * return (int)
     */
    public function getModuleIsEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_IS_ENABLE,ScopeInterface::SCOPE_STORE);
    }


    public function setRequestQueryText($querySearchText)
    {
        return $this->querySearchText = $querySearchText;
    }

    public function getRequestQueryText()
    {
        return $this->querySearchText;
    }

    public function getActionUrl($yearMakeModelParam)
    {
        // opn any direct hit on serach bar this function is called.
        echo "in get action url ";
       //exit();
        echo $this->_urlInterface->getBaseUrl();

        //exit();


        // collect
        if(!empty($yearMakeModelParam) )
        {
			 $yearMakeModelParam=trim($yearMakeModelParam);
            $yearMakeModelUrl = $this->_urlInterface->getBaseUrl().$yearMakeModelParam."-exhaust-systems";
            $redirectUrl = str_replace(' ', '-', $yearMakeModelUrl);
            $this->response->setRedirect($redirectUrl);
        }
    }

    /**
     * Get debug flag from system configuration
     * @return bool
     */
    public function isDebugEnabled()
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_IS_DEBUG_ENABLE,ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $message
     * @return \Zend\Log\Logger
     */
    public function log($message)
    {
        return $this->isDebugEnabled() ? $this->logFile()->info($message) : false;
    }

    /**
     * @return \Zend\Log\Logger
     */
    protected function logFile()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/autocompleteFinder.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        return $logger;
    }


}