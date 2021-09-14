<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Finder
 */


namespace Amasty\Finder\Block\Adminhtml\Finder\Edit\Tab\Import\Renderer;

use \Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class ImportButton extends \Magento\Backend\Block\Template implements RendererInterface
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $formName = $element->getName() == 'import_universal' ? 'universal_form' : 'images_form';

        $title = __('Import');

        return '<button class="finder-import-button" title="' . $title . '" type="submit" form="' . $formName . '">
                        <span>' . $title . '</span>
                    </button>';
    }
}
