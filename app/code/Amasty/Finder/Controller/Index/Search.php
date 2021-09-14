<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Stdlib\Cookie\Phpsession;

class Search extends \Magento\Framework\App\Action\Action
{
    const RESET = 'reset';

    /**
     * @var \Magento\Framework\Url\Decoder
     */
    private $urlDecoder;

    /**
     * @var \Amasty\Finder\Helper\Url
     */
    private $urlHelper;

    /**
     * @var \Amasty\Finder\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Amasty\Finder\Api\FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Amasty\Finder\Model\Session
     */
    private $session;

    public function __construct(
        Context $context,
        \Magento\Framework\Url\Decoder $urlDecoder,
        \Amasty\Finder\Helper\Url $urlHelper,
        \Amasty\Finder\Helper\Config $configHelper,
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Finder\Model\Session $session
    ) {
        $this->urlDecoder = $urlDecoder;
        $this->urlHelper = $urlHelper;
        $this->configHelper = $configHelper;
        $this->finderRepository = $finderRepository;
        $this->storeManager = $storeManager;
        $this->session = $session;
        parent::__construct($context);
    }

    public function execute()
    {
        $finderId = (int)$this->getRequest()->getParam('finder_id');
        if (!$finderId) {
            $norouteUrl = $this->_url->getUrl('noroute');
            $this->getResponse()->setRedirect($norouteUrl);
            return;
        }
        $this->session->removeSingleProductCookie();
        /** @var \Amasty\Finder\Model\Finder $finder */
        $finder = $this->finderRepository->getById($finderId);
        $backUrl = $this->urlDecoder->decode($this->getRequest()->getParam('back_url'));
        $currentApplyUrl = $this->urlDecoder->decode($this->getRequest()->getParam('current_apply_url'));

        $baseBackUrl = explode('?', $backUrl);
        $baseBackUrl = array_shift($baseBackUrl);

        $dropdowns = $this->getRequest()->getParam('finder');
        if ($dropdowns) {
            $finder->saveFilter(
                $dropdowns,
                $this->getRequest()->getParam('category_id'),
                [$currentApplyUrl, $baseBackUrl]
            );
        }

        if ($this->configHelper->getConfigValue('advanced/clear_other_conditions')) {
            $finders = $this->finderRepository->getWithoutId($finder->getId());
            foreach ($finders as $item) {
                $item->resetFilter();
            }
        }
        
        $backUrl = $this->urlHelper->getUrlWithFinderParam($backUrl, $finder->getUrlParam(), true);
        if ($this->getRequest()->getParam(self::RESET)) {
            $finder->resetFilter();
            $resetConfig = $this->configHelper->getConfigValue('general/reset_home');

            if ($resetConfig == \Amasty\Finder\Model\Source\Reset::VALUE_HOME) {
                $backUrl = $this->storeManager->getStore()->getBaseUrl();
            } else {
                $resetUrl = $this->urlDecoder->decode($this->getRequest()->getParam('reset_url'));
                $backUrl = $finder->removeGet($resetUrl, \Amasty\Finder\Helper\Url::FINDER_URL_PARAM);
            }
        }

        $this->getResponse()->setRedirect($backUrl);
    }
}
