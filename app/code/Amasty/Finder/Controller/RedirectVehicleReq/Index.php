<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */

namespace Amasty\Finder\Controller\RedirectVehicleReq;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);

    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        echo 'Require Vehicle Request Form...';
        return $this->_pageFactory->create();
    }
}
