<?php
namespace Magebees\Ajaxcomparewishlist\Plugin\Controller\Product\Compare;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Catalog\Controller\Product\Compare\Add
{
    public function afterExecute(\Magento\Catalog\Controller\Product\Compare\Add $subject, $result)
    {
        if ($subject->getRequest()->isAjax()) {
            $this->_view->loadLayout();
            $productId = (int)$this->getRequest()->getParam('product');
            $storeId = $this->_storeManager->getStore()->getId();			if($productId):
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }			endif;
            if ($product) {
                $this->_catalogProductCompareList->addProduct($product);
                $this->_eventManager->dispatch('catalog_product_compare_add_product', ['product' => $product]);
                $response = [];
                $popup    = $this->_view->getLayout()->createBlock('Magento\Catalog\Block\Product\AbstractProduct')
                                        ->setData('product', $product)
                                        ->setTemplate('Magebees_Ajaxcomparewishlist::comparepopup.phtml')
                                        ->toHtml();
                $response['success'] = true;
                $response['popup']   = $popup;
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($response);
                $this->_objectManager->get(\Magento\Catalog\Helper\Product\Compare::class)->calculate();
                return $resultJson;
            }
            return false;
        }
        return $result;
    }
}