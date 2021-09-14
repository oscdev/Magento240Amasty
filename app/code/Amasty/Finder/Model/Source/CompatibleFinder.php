<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class CompatibleFinder implements ArrayInterface
{
    const ONLY_ACTIVE_FINDER = 0;

    /**
     * @var \Amasty\Finder\Api\FinderRepositoryInterface
     */
    private $finderRepository;

    public function __construct(
        \Amasty\Finder\Api\FinderRepositoryInterface $finderRepository
    ) {
        $this->finderRepository = $finderRepository;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $finders = $this->toArray();
        $result = [['value' => self::ONLY_ACTIVE_FINDER, 'label' => __('Currently Applied Finder')]];

        foreach ($finders as $finder) {
            $result[] = ['value' => $finder->getFinderId(), 'label' => $finder->getName()];
        }

        return $result;
    }

    /**
     * @return \Amasty\Finder\Api\Data\FinderInterface[]
     */
    public function toArray()
    {
        return $this->finderRepository->getList();
    }
}
