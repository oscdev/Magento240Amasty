<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Cache\StateInterface;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Magento\CatalogSearch\Model\ResourceModel\EngineProvider;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PAGE_CACHE_TYPE = 'full_page';

    /**
     * @var StateInterface
     */
    private $cacheState;

    public function __construct(
        Context $context,
        StateInterface $cacheState
    ) {
        parent::__construct($context);
        $this->cacheState = $cacheState;
    }

    /**
     * @param $path
     * @return string|bool|int
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue('amfinder/' . $path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check current page cache status and application type
     *
     * @return bool
     */
    public function isVarnish()
    {
        $cachingApplication =  $this->scopeConfig->getValue(
            'system/full_page_cache/caching_application',
            ScopeInterface::SCOPE_STORE
        );
        return $this->cacheState->isEnabled(self::PAGE_CACHE_TYPE)
            && $cachingApplication == PageCacheConfig::VARNISH;

    }

    /**
     * @return bool
     */
    public function isMysqlEngine()
    {
        return strpos($this->scopeConfig->getValue(EngineProvider::CONFIG_ENGINE_PATH), 'mysql') !== false;
    }
}
