<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Model;

use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Session\StorageInterface;
use Magento\Framework\Session\ValidatorInterface;

class Session extends \Magento\Framework\Session\SessionManager
{
    const SESSION_KEY = 'amfinder_saved_values';
    const SINGLE_PRODUCT = 'single_product';
    const SINGLE_PRODUCT_COOKIE = 'amfinder_single_product_flag';
    const FINDER_ROUTES = 'amfinder_routes';

    /** @var array */
    private $data = [];

    /**
     * @var \Amasty\Finder\Helper\Config
     */
    private $configHelper;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        SidResolverInterface $sidResolver,
        ConfigInterface $sessionConfig,
        SaveHandlerInterface $saveHandler,
        ValidatorInterface $validator,
        StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        \Amasty\Finder\Helper\Config $configHelper
    ) {
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState
        );
        $this->loadData();
        $this->configHelper = $configHelper;
    }

    /**
     * @param int $finderID
     *
     * @return array|null
     */
    public function getFinderData($finderID)
    {
        if (isset($this->data[$finderID])) {
            return $this->data[$finderID];
        }
        return null;
    }

    /**
     * @param int $finderID
     * @param array $finderData
     *
     * @return $this
     */
    public function setFinderData($finderID, $finderData)
    {
        $this->data[$finderID] = $finderData;
        $this->saveData();
        $this->setFinderRoutesCookie();
        return $this;
    }

    /**
     * Set finder applied routes if varnish cache used
     *
     * @return $this
     */
    private function setFinderRoutesCookie()
    {
        if ($this->configHelper->isVarnish()) {
            $finders = $this->getAllFindersData();
            $finders = is_array($finders) ? $finders : [];
            $routes = [];
            foreach ($finders as $finderData) {
                if (isset($finderData[Finder::APPLY_URL])) {
                    foreach ($finderData[Finder::APPLY_URL] as $url) {
                        $result = preg_match('/[a-z]+?\/([^\.\?]+).*$/', $url, $matches);
                        if ($result && isset($matches[1])) {
                            $routes[] = '_' . str_replace("/", "_", $matches[1]) . '_';
                        }
                    }
                }
            }

            $appliedRoutes = $this->cookieManager->getCookie(self::FINDER_ROUTES, "");
            $appliedRoutes = empty($appliedRoutes) ? [] : explode(' ', $appliedRoutes);
            $routes = array_unique(array_merge($routes, $appliedRoutes));

            $this->cookieManager->deleteCookie(self::FINDER_ROUTES);

            if (!empty($routes)) {
                $metadata = $this->cookieMetadataFactory
                    ->createPublicCookieMetadata()
                    ->setDuration(86400)
                    ->setPath($this->getCookiePath())
                    ->setDomain($this->getCookieDomain());
                $this->cookieManager->setPublicCookie(
                    self::FINDER_ROUTES,
                    implode(' ', $routes),
                    $metadata
                );
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getAllFindersData()
    {
        return $this->data;
    }

    /**
     * @param int $finderID
     *
     * @return $this
     */
    public function reset($finderID)
    {
        unset($this->data[$finderID]);
        $this->removeSingleProductCookie();
        $this->saveData();
        $this->setFinderRoutesCookie();
        return $this;
    }

    /**
     * @return $this
     */
    public function resetSingleProduct()
    {
        unset($this->data[self::SINGLE_PRODUCT]);
        $this->saveData();
        return $this;
    }

    /**
     * Save session data
     * @return void
     */
    private function saveData()
    {
        $this->setData(self::SESSION_KEY, $this->data);
    }

    /**
     * Get session data
     * @return void
     */
    private function loadData()
    {
        $this->data = $this->getData(self::SESSION_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setSingleProduct()
    {
        $this->data[self::SINGLE_PRODUCT] = true;
        $this->saveData();
    }

    /**
     * @return bool
     */
    public function isSingleProduct()
    {
        return isset($this->data[self::SINGLE_PRODUCT]) ?: false;
    }

    /**
     * @inheritdoc
     */
    public function setSingleProductCookie($value)
    {
        $this->cookieManager->setPublicCookie(self::SINGLE_PRODUCT_COOKIE, $value, $this->getMetadata());
    }

    /**
     * @inheritdoc
     */
    public function getSingleProductCookieValue()
    {
        return $this->cookieManager->getCookie(self::SINGLE_PRODUCT_COOKIE);
    }

    /**
     * @inheritdoc
     */
    public function removeSingleProductCookie()
    {
        $this->cookieManager->deleteCookie(self::SINGLE_PRODUCT_COOKIE, $this->getMetadata());
        return $this;
    }

    /**
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadata
     */
    private function getMetadata()
    {
        return $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(86400)
            ->setPath($this->getCookiePath())
            ->setDomain($this->getCookieDomain())
            ->setHttpOnly(false);
    }
}
