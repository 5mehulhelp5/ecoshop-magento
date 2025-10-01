<?php
namespace Magebees\Advertisementblock\Model\Widget\Config;

class Advertisementblock implements \Magento\Framework\Option\ArrayInterface
{	
	protected $advertisementinfoFactory;
	
    public function __construct(
        \Magebees\Advertisementblock\Model\AdvertisementinfoFactory $advertisementinfoFactory
    ) {
        $this->advertisementinfoFactory = $advertisementinfoFactory;
    }

    public function toOptionArray()
    {
        return $this->getAdvertisementArr();
    }
    public function getAdvertisementArr()
    {
        $adv_arr=[];
        $adv_model=$this->advertisementinfoFactory->create();
        $collection = $adv_model->getCollection()->getData();
        foreach ($collection as $coll) {
            $adv_id=$coll['advertisement_id'];
            $block_name=$coll['block_name'];
            $adv_arr[$adv_id]=$block_name;
        }
        return $adv_arr;
    }
}
