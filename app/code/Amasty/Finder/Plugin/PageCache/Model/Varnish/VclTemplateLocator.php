<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Plugin\PageCache\Model\Varnish;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Stdlib\StringUtils;
use Magento\PageCache\Model\Varnish\VclTemplateLocator as MagentoVclTplLocator;

class VclTemplateLocator
{
    const CHECKOUT_SKIP_CONDITION = 'if (req.url ~ "/checkout"';
    const VARNISH_FIXTURE_PATH = 'fixtures/varnish.vcl.fixture';

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StringUtils
     */
    private $stringUtils;

    public function __construct(
        Reader $reader,
        ReadFactory $readFactory,
        ScopeConfigInterface $scopeConfig,
        StringUtils $stringUtils
    ) {
        $this->reader = $reader;
        $this->readFactory = $readFactory;
        $this->scopeConfig = $scopeConfig;
        $this->stringUtils = $stringUtils;
    }

    public function afterGetTemplate(
        MagentoVclTplLocator $subject,
        string $vclData
    ): string {
        $insertPosition = $this->getInsertPosition($vclData);

        if ($insertPosition === null) {
            throw new \InvalidArgumentException(__('Invalid vcl configuration provided'));
        }

        return sprintf(
            '%s%s%s%s',
            $this->stringUtils->substr($vclData, 0, $insertPosition),
            PHP_EOL,
            $this->getFixtureText(),
            $this->stringUtils->substr($vclData, $insertPosition)
        );
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getFixtureText(): string
    {
        $moduleEtcPath = $this->reader->getModuleDir(Dir::MODULE_ETC_DIR, 'Amasty_Finder');
        $fixturePath = sprintf('%s/%s', $moduleEtcPath, self::VARNISH_FIXTURE_PATH);
        $directoryRead = $this->readFactory->create($moduleEtcPath);
        $fixturePath = $directoryRead->getRelativePath($fixturePath);

        return $directoryRead->readFile($fixturePath);
    }

    private function getInsertPosition(string $vclData): ?int
    {
        $checkoutSkipConditionPosition = $this->stringUtils->strpos($vclData, self::CHECKOUT_SKIP_CONDITION);

        if (false !== $checkoutSkipConditionPosition) {
            $endOfCheckoutSkipConditionPosition = $this->stringUtils->strpos(
                $vclData,
                '}',
                $checkoutSkipConditionPosition
            );

            return $endOfCheckoutSkipConditionPosition + 1;
        }

        return null;
    }
}
