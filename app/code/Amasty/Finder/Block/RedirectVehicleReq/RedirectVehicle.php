<?php
namespace Amasty\Finder\Block\RedirectVehicleReq;

class RedirectVehicle extends \Magento\Framework\View\Element\Template
{
    public function __construct(\Magento\Framework\View\Element\Template\Context $context)
    {
        parent::__construct($context);
    }

    public function displayVehicleReqForm()
    {
        //echo 'vehicle block';
        return __('Hello World');
    }
}