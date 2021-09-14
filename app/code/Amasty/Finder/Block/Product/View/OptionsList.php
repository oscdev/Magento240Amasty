<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Block\Product\View;

class OptionsList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Finder::product/view/finder_options.phtml';

    /**
     * @var \Amasty\Finder\Api\MapRepositoryInterface
     */
    private $mapRepository;

    /**
     * @var \Amasty\Finder\Model\Session
     */
    private $session;

    /**
     * @var array
     */
    private $dropdownNames = [];

    /**
     * @var \Amasty\Finder\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Amasty\Finder\Api\DropdownRepositoryInterface
     */
    private $dropdownRepository;

    /**
     * @var array
     */
    private $options = [];

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Finder\Api\MapRepositoryInterface $mapRepository,
        \Amasty\Finder\Model\Session $session,
        \Amasty\Finder\Helper\Config $configHelper,
        \Amasty\Finder\Api\DropdownRepositoryInterface $dropdownRepository,
        array $data = []
    ) {
        $this->mapRepository = $mapRepository;
        $this->session = $session;
        $this->configHelper = $configHelper;
        $this->dropdownRepository = $dropdownRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getResponseData()
    {
        return ['options' => count($this->getFinderOptions()), 'html' => $this->toHtml()];
    }

    /**
     * @return array
     */
    public function getFinderOptions()
    {
        $finderId = $this->getFinderId();

        if (!isset($this->options[$finderId])) {
            $dropdowns = $this->dropdownRepository->getByFinderId($finderId);
            $names = [];

            if ($dropdowns) {
                $productId = $this->getProductId();
                $dropdownIds = $this->getDropdownIds($dropdowns);

                $finderValues = $this->mapRepository->getDependsValues($productId, $dropdownIds);

                foreach ($finderValues as $finderValue) {
                    foreach ($finderValue->getData() as $key => $value) {
                        if (strpos($key, 'value_name') !== false) {
                            $names[$finderValue->getId()][] = $value;
                        }
                    }
                    $names[$finderValue->getId()] = array_reverse($names[$finderValue->getId()]);
                }
            }

            usort($names, [$this, 'sortOptions']);
            $this->options[$finderId] = $names;
        }

        return $this->options[$finderId];
    }

    /**
     * @param $first
     * @param $second
     * @return int
     */
    private function sortOptions($first, $second)
    {
        $first = implode('', $first);
        $second = implode('', $second);

        return strnatcmp($first, $second) < 0 ? -1 : 1;
    }

    /**
     * @return int
     */
    private function getFinderId()
    {
        $finderId = $this->configHelper->getConfigValue('advanced/compatible_finder');

        if (!$finderId) {
            $activeFinders = $this->session->getAllFindersData();
            if ($activeFinders) {
                $finderIds = array_keys($activeFinders);
                $finderId = array_shift($finderIds);
            }
        }

        return (int)$finderId;
    }

    /**
     * @param $dropdowns
     * @return array
     */
    private function getDropdownIds($dropdowns)
    {
        $dropdownIds = [];

        foreach ($dropdowns as $dropdown) {
            $this->dropdownNames[] = $dropdown->getName();
            $dropdownIds[] = $dropdown->getId();
        }

        return $dropdownIds;
    }

    /**
     * @return array
     */
    public function getDropdownNames()
    {
        return $this->dropdownNames;
    }
}
