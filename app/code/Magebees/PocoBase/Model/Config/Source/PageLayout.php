<?php
namespace Magebees\PocoBase\Model\Config\Source;
class PageLayout implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;
	
    /**
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     */
    public function __construct(\Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder)
    {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
    }
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
		$last = $this->pageLayoutBuilder->getPageLayoutsConfig()->toOptionArray();	
		
		unset($last[0]);
		//unset ($last[count($last)-1]);		
		//unset ($last[4]);	
		unset ($last[5]);	
		unset ($last[6]);		
		unset ($last[7]);
		return $last;
	}
}

