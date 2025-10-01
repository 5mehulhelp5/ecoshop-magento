<?php
/***************************************************************************
 Extension Name : Product Label
 Extension URL  : https://www.magebees.com/product-label-extension-magento-2.html
 Copyright      : Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email  : support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
    protected $date;
    protected $_filesystem;
    protected $_productlabelModel;
    protected $_customerSession;		protected $_imageFactory;		protected $request;
    protected $_labels = null;
    protected $_sizes = [];
    
    //productlabel wide
    const MODULE_ENABLE = 'productlabel/general/enable';
    const DISPLAY_LABEL_ON_PAGES = 'productlabel/general/display_label_on';
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magebees\Productlabel\Model\Productlabel $productlabelModel,
		\Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Request\Http $request,
        Filesystem $filesystem
       
    ) {
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_productlabelModel = $productlabelModel;
		$this->_customerSession = $customerSession;
        $this->request = $request;
        $this->date = $date;
        parent::__construct($context);
    }
    
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue($config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function getCustomerGroupId()
    {
        return $this->_customerSession->getCustomerGroupId();
    }
    
     /**
      * Check whether it is single store mode
      *
      * @return bool
      */
    public function isSingleStoreMode()
    {
        return (bool)$this->_storeManager->isSingleStoreMode();
    }
    
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore(true)->getId();
    }

    public function isProductlabelEnabled()
    {
        return $this->getConfig('productlabel/general/enable');
    }
    
    public function getConfigDisplayLabelOnArray()
    {
        $pages = $this->getConfig('productlabel/general/display_label_on');
        return explode(',', (string)$pages);
    }

    public function getImageUrl($label)
    {
        $mediaBaseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        if ($label->getMode() === "prod" && $label->getProdImage()) {
            return $mediaBaseUrl . 'prod_image/resized' . $label->getProdImage();
        }

        if ($label->getMode() === "cat" && $label->getCatImage()) {
            return $mediaBaseUrl . 'cat_image/resized' . $label->getCatImage();
        }

        return false;
    }

    public function getImagePath($label)
    {
        $mediaDirectory = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath();

        if ($label->getMode() === "prod" && $label->getProdImage()) {
            return $mediaDirectory . 'prod_image/resized' . $label->getProdImage();
        }

        if ($label->getMode() === "cat" && $label->getCatImage()) {
            return $mediaDirectory . 'cat_image/resized' . $label->getCatImage();
        }

        return false;
    }

    public function getCurrentDate()
    {
        return $this->date->gmtDate();
    }

    public function getLabelCollection()
    {
        $storeId = $this->getCurrentStoreId();
        $groupId = $this->getCustomerGroupId();

        $labelCollection = $this->_productlabelModel->getCollection()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('customer_group_ids', [['finset' => $groupId]])
            ->setOrder('sort_order', 'ASC')
            ->setOrder('label_id', 'ASC');

        if (!$this->isSingleStoreMode()) {
            $labelCollection->addFieldToFilter('stores', [['finset' => $storeId]]);
        }

        return $labelCollection;
    }

    public function getLabel($product, $mode = 'category')
    {
        if (!$this->isProductlabelEnabled()) {
            return '';
        }

        $labels = $this->getLabelCollection();
        if (!$labels->getSize()) {
            return '';
        }

        $html = '';
        $applied = false;

        foreach ($labels as $label) {
            $label->setProduct($product);
            $label->init($mode, $product);

            if ($label->getHide() && $applied) {
                continue;
            }

            if ($label->isValid()) {
                $applied = true;
                $html .= $this->_generateHtml($label);
            }
        }

        return $html;
    }

    protected function _generateHtml($label)
    {
        $imgUrl = $this->getImageUrl($label);
        $imgPath = $this->getImagePath($label);

        if (!$imgUrl) {
            return '';
        }

        if (empty($this->_sizes[$imgUrl])) {
            $imageInfo = getimagesize($imgPath);
            $this->_sizes[$imgUrl] = [
                'w' => $imageInfo[0],
                'h' => $imageInfo[1],
            ];
        }

        $size = $this->_sizes[$imgUrl];
        $tableClass = $label->getCssClass();
        $tableStyle = "height: {$size['h']}px; width: {$size['w']}px; " . $this->_getPositionAdjustment($tableClass, $size);

        $textStyle = $this->_getTextStyle($label);

        $html = '<div class="prodLabel ' . $tableClass . '" style="' . $tableStyle . '">';
        $html .= '<div style="background: url(' . $imgUrl . ') no-repeat 0 0">';

        if ($label->getText()) {
            $html .= '<span class="productlabel-txt" ' . $textStyle . '>' . $label->getText() . '</span>';
        }

        $html .= '</div></div>';

        return $html;
    }

    protected function _getTextStyle($label)
    {
        $color = $label->getMode() === 'cat' ? $label->getCatTextColor() : $label->getProdTextColor();
        $size = $label->getMode() === 'cat' ? $label->getCatTextSize() : $label->getProdTextSize();

        $style = "color: {$color};";
        if ($size) {
            $style .= "font-size: {$size}px;";
        }

        return 'style="' . $style . '"';
    }

    protected function _getPositionAdjustment($tableClass, $sizes)
    {
        $style = '';

        if (strpos($tableClass, 'center') !== false) {
            $style .= 'margin-left: ' . (-$sizes['w'] / 2) . 'px;';
        }

        if (strpos($tableClass, 'middle') !== false) {
            $style .= 'margin-top: ' . (-$sizes['h'] / 2) . 'px;';
        }

        return $style;
    }
    
    public function resizeImg($fieldName, $fileName, $width, $height)
    {
        $dir = "resized";
        if (trim($width) == "" || trim($width) < 0) {
            $width = "80";
        }
        if (trim($height) == "" || trim($height) < 0) {
            $height = "80";
        }
                
        $mediaDir = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $mediaDir->create($fieldName);
        $mediaDir->changePermissions($fieldName, 0775);
        $fieldName = $mediaDir->getAbsolutePath($fieldName);
        $absPath = $fieldName.$fileName;
        $imageResized = $fieldName."/".$dir.$fileName;
        
        if ($width != '') {
            if (file_exists($imageResized)) {
                unlink($imageResized);
            }
            $imageResize = $this->_imageFactory->create();
            $imageResize->open($absPath);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(true);
            $imageResize->resize($width, $height);
            $dest = $imageResized ;
            $imageResize->save($dest);
        }
        return true;
    }
}
