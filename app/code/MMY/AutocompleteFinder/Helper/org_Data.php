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


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

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
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * return (int)
     */
    public function getModuleIsEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_IS_ENABLE,ScopeInterface::SCOPE_STORE);
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