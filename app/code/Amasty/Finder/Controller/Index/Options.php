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

use Amasty\Finder\Api\DropdownRepositoryInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;

class Options extends \Magento\Framework\App\Action\Action
{
    /**
     * @var DropdownRepositoryInterface
     */
    private $dropdownRepository;

    /**
     * @var \Magento\Framework\View\Layout
     */
    private $layout;

    public function __construct(
        Context $context,
        \Amasty\Finder\Api\DropdownRepositoryInterface $dropdownRepository,
        \Magento\Framework\View\Layout $layout
    ) {
        $this->dropdownRepository = $dropdownRepository;
        $this->layout = $layout;
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
        $parentValueId = $this->getRequest()->getParam('parent_id', false);
        $dropdownId = $this->getRequest()->getParam('dropdown_id');
        $response = '';

        if ($parentValueId !== false && $dropdownId) {
            try {
                /** @var \Amasty\Finder\Model\Dropdown $dropdown */
                $dropdown = $this->dropdownRepository->getById($dropdownId);

                $options = $this->getOptions($dropdown, $parentValueId);

                $response = $this->layout->createBlock(\Amasty\Finder\Block\DropdownRenderer::class)
                    ->setDropdown($dropdown)
                    ->setFinder($dropdown->getFinder())
                    ->setParentDropdownId((int)$this->getRequest()->getParam('parent_dropdown_id'))
                    ->setValues($options)
                    ->toHtml();

            } catch (NoSuchEntityException $e) {
                //do nothing
            }
        }

        return $this->getResponse()->setBody($response);
    }

    /**
     * @param $dropdown
     * @param int $parentValueId
     * @return array
     */
    private function getOptions($dropdown, $parentValueId)
    {
        $useSavedValues = $this->getRequest()->getParam('use_saved_values', 0);

        $selectedValue = $useSavedValues ? $dropdown->getFinder()->getSavedValue($dropdown->getId()) : 0;

        $options = $dropdown->getOptions($parentValueId, $selectedValue, true);

        return $options;
    }
}
