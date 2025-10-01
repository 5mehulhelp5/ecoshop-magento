<?php
namespace Magebees\Layerednavigation\Controller\Brand;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;

class Index extends \Magento\Framework\App\Action\Action
{
   protected $brands;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magebees\Layerednavigation\Model\Brands $brands
    ) {
        parent::__construct($context);
        $this->brands = $brands;
    }
    public function execute()
    {
        $url1 = $this->getRequest()->getParam('brand_url');
        $this->_view->loadLayout();
        $url = $this->brands->getCollection()
                    ->addFieldToFilter('seo_url', ['eq'=>$url1]);
        $brand=$url->getData();
        if (isset($brand['0'])) {
            $brandname=$brand['0']['brand_name'];
        }
        $this->_view->renderLayout();
    }
}
