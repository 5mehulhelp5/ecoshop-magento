<?php
/***************************************************************************
 Extension Name : Product Label
 Extension URL  : https://www.magebees.com/product-label-extension-magento-2.html
 Copyright      : Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email  : support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Block\Adminhtml\Productlabel\Helper;

use Magento\Framework\UrlInterface;

class Product extends \Magento\Framework\Data\Form\Element\Image
{
     /**
      * @var UrlInterface
      */
    protected $_urlBuilder;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        UrlInterface $urlBuilder,
        $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $urlBuilder, $data);
        //$this->setType('file');
    }
    
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]).'prod_image'.$this->getValue();
        }
        return $url;
    }
}
