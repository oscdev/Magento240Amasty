<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Controller\Product;

use Magento\Framework\App\Action\Context;

class Options extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Amasty\Finder\Block\Product\View\OptionsList
     */
    private $optionsList;

    public function __construct(
        Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Amasty\Finder\Block\Product\View\OptionsList $optionsList
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->optionsList = $optionsList;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $this->optionsList->setProductId((int) $this->getRequest()->getParam('product_id'));
        $response = $this->jsonEncoder->encode($this->optionsList->getResponseData());

        return $this->getResponse()->setBody($response);
    }
}
