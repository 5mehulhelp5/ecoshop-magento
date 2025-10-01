<?php 
namespace Magebees\PocoThemes\Plugin\Product;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Catalog\Model\Product;
class AddToButtons
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
	
	 public function afterToHtml(
        \Magento\Catalog\Block\Product\Image $subject,
        $result
    ) {
        $product = $subject->getProduct();
        /*$product = $subject->getProduct();
        $moduleName = $this->request->getModuleName();
        if ($product && $moduleName !== 'checkout') {
            $result .= $this->_helper->renderProductLabel($product, 'category');
            $this->registry->register('amlabel_category_observer', $product, true);
        }*/
        //print_r(get_class_methods($subject));exit;
        $layout = $subject->getLayout();
       // $result .= $layout->createBlock('Magebees\PocoThemes\Block\AddToButtons')->setProduct($product)->toHtml();
        //$addToButtonsBlock = $layout->createBlock('Magebees\PocoThemes\Block\AddToButtons', 'hover.add.to.buttons');
	    //$layout->getBlock('category.product.addto')->insert($addToButtonsBlock);
        return $result;
    }



}
