<?php

namespace Magebees\Advertisementblock\Block\Widget;

class Advertisementwidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $advertisementimagesFactory;
	protected $advertisementinfoFactory;
	protected $_storeManager;
	protected $_data;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magebees\Advertisementblock\Model\AdvertisementimagesFactory $advertisementimagesFactory,
		 \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magebees\Advertisementblock\Model\AdvertisementinfoFactory $advertisementinfoFactory
    ) {
        parent::__construct($context);
        $this->advertisementimagesFactory = $advertisementimagesFactory;
        $this->advertisementinfoFactory = $advertisementinfoFactory;
		$this->_storeManager=$storeManager;
    }

	public function getStoreObj(){
		return $this->_storeManager;
	}

    public function addData(array $arr)
    {
        
        $this->_data = array_merge($this->_data, $arr);
    }

    public function setData($key, $value = null)
    {
        
        $this->_data[$key] = $value;
    }
 
    public function _toHtml()
    {
        
		$load_ajax = $this->getData('wd_load_ajax');
		if($load_ajax){
		$this->setTemplate('Magebees_PocoBase::load_advertisementblock.phtml');
		}
		if (($this->getData('template'))&&(!$load_ajax)) {
            $this->setTemplate($this->getData('template'));
        }
        $enabled=$this->_scopeConfig->getValue('advertisementblock/setting/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->setEnabled($enabled);
        $this->setTitle($this->getWdTitle());
        $this->setSubtitle($this->getWdSubtitle());
        $this->setAdvertisement($this->getWdAdvertisement());
        $this->setStyle((int)$this->getWdStyle());
        return parent::_toHtml();
    }
    public function getAdvertiseImagedetail($adv_id)
    {
        $adv_model=$this->advertisementimagesFactory->create();
        $collection = $adv_model->getCollection()->addFieldToFilter('advertisement_id', $adv_id);
        return $collection;
    }
    public function getAdvertiseBlockdetail($adv_id)
    {
        $adv_model=$this->advertisementinfoFactory->create();
        $collection = $adv_model->getCollection()->addFieldToFilter('advertisement_id', $adv_id);
        $data=$collection->getData();
        return $data;
    }
     //for posh theme
    public function getIdByUniqueCode($code){
        $adv_model=$this->advertisementinfoFactory->create();
        $id = 0;
        $id = $adv_model->load($code,'unique_code')->getId();
        return $id;
    }
}
