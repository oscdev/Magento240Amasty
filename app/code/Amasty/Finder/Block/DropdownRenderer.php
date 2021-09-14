<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Block;

class DropdownRenderer extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Finder\Model\Dropdown
     */
    private $dropdownModel;

    /**
     * @var string
     */
    protected $_template = 'dropdown-renderer.phtml';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Finder\Model\Dropdown $dropdownModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dropdownModel = $dropdownModel;
    }

    /**
     * @param \Amasty\Finder\Model\Dropdown $dropdown
     *
     * @return array
     */
    public function getDropdownValues(\Amasty\Finder\Model\Dropdown $dropdown)
    {
        $values = [];

        $parentValueId = $this->getFinder()->getSavedValue($this->getParentDropdownId());
        $currentValueId = $this->getFinder()->getSavedValue($dropdown->getId());

        $isDisableDropdown = $this->dropdownModel->isHidden($dropdown, $this->getFinder())
            && !$parentValueId && !$currentValueId;
        if (!$isDisableDropdown) {
            if (strpos($this->_urlBuilder->getCurrentUrl(), 'find=') === false) {
                $currentValueId = 0;
            }

            $values = $dropdown->getOptions($parentValueId, $currentValueId);
        }

        return $values;
    }
}
