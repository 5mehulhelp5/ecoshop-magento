<?php
namespace Magebees\CacheWarmer\Block\Adminhtml\Form\Field;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class PageType extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var \Magento\Framework\View\Layout
     */
    private $layout;

    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        \Magento\Framework\View\Layout $layout,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->layout = $layout;
    }

    public function getElementHtml()
    {
        /** @var \Magebees\CacheWarmer\Block\Adminhtml\Form\Field\PageType\Element $element */
        $element = $this->layout->createBlock(\Magebees\CacheWarmer\Block\Adminhtml\Form\Field\PageType\Element::class);

        $element
            ->setValue($this->getData('value'))
            ->setName($this->getData('name'));

        return $element->toHtml();
    }
}
