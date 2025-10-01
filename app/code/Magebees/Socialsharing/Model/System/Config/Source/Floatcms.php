<?php
namespace Magebees\Socialsharing\Model\System\Config\Source;
use Magento\Cms\Model\PageFactory;

class Floatcms extends OptionArray
{
    protected $_pageFactory;
    public function __construct(PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
    }
    public function getOptionHash()
    {
        $pages = $this->_pageFactory->create()->getCollection();
        $cmsPages = [];

        foreach ($pages as $page) {
            $cmsPages[$page->getId()] = $page->getTitle();
        }
        return $cmsPages;
    }
}