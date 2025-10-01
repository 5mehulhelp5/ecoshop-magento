<?php 
namespace Magebees\PocoThemes\Plugin\Product;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Catalog\Model\Product;
class View
{
    protected $scopeConfig;
	protected $_helper;
	public function __construct(
		\Magebees\PocoThemes\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
		$this->_helper = $helper;
    }
	
	public function beforeInitProductLayout(
        \Magento\Catalog\Helper\Product\View $subject,
        ResultPage $resultPage, Product $product, $params = null
    ) {
		$layout = $resultPage->getLayout();
		$store_id = $this->_helper->getStoreId();
		$move_tab = $this->_helper->getProductDetail('move_tab',$store_id);
		if($move_tab){
			$layout->getUpdate()->addHandle('move_product_tab');
		}
		return [$resultPage, $product, $params];
    }
}
