<?php
namespace Magebees\Onepagecheckout\Controller\Quote;

class Update extends \Magento\Framework\App\Action\Action {
   
    protected $_sidebar;
    protected $_resultJsonFactory;
    protected $_jsonHelper;
    protected $_dataObjectFactory;
    protected $_cartTotalRepositoryInterface;
    protected $cart;
    protected $checkoutSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepositoryInterface,
        \Magento\Checkout\Model\Sidebar $sidebar,
		\Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Cart $cart
    ) {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_sidebar = $sidebar;
        $this->_cartTotalRepositoryInterface = $cartTotalRepositoryInterface;
        $this->cart = $cart;
    }

    public function execute()
    {
		$itemId = (int)$this->getRequest()->getParam('itemId');
        $itemQty = (int)$this->getRequest()->getParam('qty');
		$updateType = $this->getRequest()->getParam('updateType');
		$flag = $this->getRequest()->getParam('flag');
		$productData = $this->_objectManager->get('Magento\Quote\Model\Quote\Item')->load($itemId);
		$pid = $productData->getProductId();
        $result = array();
        $result['error'] = '';
		$item = $this->cart->getQuote()->getItemById($itemId);
        $oldQty = $item->getQty();
       try {
            if ($updateType == 'update') {
				$stockRegistry = $this->_objectManager->create('\Magento\CatalogInventory\Api\StockRegistryInterface');
				$productStock = $stockRegistry->getStockItem($pid);
					if($productStock->getItemId()):
						if($flag == "inc"){
							$itemQty = $itemQty + $productStock->getQtyIncrements();
						}else if($flag == "dec"){
							$itemQty = $itemQty - $productStock->getQtyIncrements();
						}else if($flag == "upqty"){
							$itemQty = $itemQty;
						}
						$use_config_enable_qty_inc = $productStock->getUseConfigEnableQtyInc();
						if($use_config_enable_qty_inc):
							if($flag == "inc"){
									$itemQty = $itemQty + $productStock->getUseConfigMinSaleQty();
							}else if($flag == "dec"){
								$itemQty = $itemQty - $productStock->getUseConfigMinSaleQty();
							}else if($flag == "upqty"){
								$itemQty = $itemQty;
							}
						endif;
					else:
						if($flag == "inc"){
							$itemQty = $itemQty + 1;
						}else if($flag == "dec"){
							$itemQty = $itemQty - 1;
						}else if($flag == "upqty"){
							$itemQty = $itemQty;
						}
					endif;
				$configManagestock = $productStock->getData("use_config_manage_stock");
				$manageStock = $productStock->getData("manage_stock");
				$proQty = $productStock->getData("qty");
				$typeId = $productStock->getData("type_id");
				if($typeId == 'simple'){
					if($configManagestock ==1 || $manageStock == 1){
						if($itemQty > $proQty){
							$result['error'] = __("The requested qty is not available");
							$resultJson = $this->_resultJsonFactory->create();
							return $resultJson->setData($result);
						}
					}
				}
				
                $this->_sidebar->checkQuoteItem($itemId);
                $this->_sidebar->updateQuoteItem($itemId, $itemQty);
            } else {
                $this->_sidebar->removeQuoteItem($itemId);
            }

        } catch (\Exception $e) {
            $this->_sidebar->updateQuoteItem($itemId, $oldQty);
            $result['error'] = $e->getMessage();
        }
		
        if($this->cart->getSummaryQty() == 0){
            $result['cartEmpty'] = true;
        }

        if ($this->cart->getQuote()->isVirtual()) {
            $result['is_virtual'] = true;
        } else {
            $result['is_virtual'] = false;
        }
        
        $resultJson = $this->_resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}