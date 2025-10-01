<?php
/***************************************************************************
 Extension Name : Product Label
 Extension URL  : https://www.magebees.com/product-label-extension-magento-2.html
 Copyright      : Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email  : support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Block\Adminhtml\Productlabel\Renderer;

use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class ProductImage extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
   
    private $_storeManager;
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Context $context, StoreManagerInterface $storemanager, array $data = [])
    {
        $this->_storeManager = $storemanager;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }
    /**
     * Renders grid column
     *
     * @param DataObject $row
     * @return  string
     */
    public function render(DataObject $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        if ($value) {
            $src = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
            $width = 50;
            $height = 50;
            return "<img src='".$src.'prod_image'.$value."' width=".$width." height=".$height." alt='No Image Avialable'/>";
        } else {
            return "No Image Avialable";
        }
    }
}
