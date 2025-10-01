<?php
namespace Magebees\PocoBase\Model\Config\Source;
class StoreFeatures implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $blockColFactory;

    /**
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     */
    public function __construct(\Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockColFactory)
    {
        $this->blockColFactory = $blockColFactory;
    }
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
		$collection = $this->blockColFactory->create();
		$blockdata = array();
		foreach($collection as $data) {	
			$blockdata[] = array('value'=>$data['identifier'], 'label'=>__($data['title']));
		}
	    return $blockdata;
	}
}

