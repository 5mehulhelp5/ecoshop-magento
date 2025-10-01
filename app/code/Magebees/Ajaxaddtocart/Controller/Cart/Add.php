<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Ajaxaddtocart\Controller\Cart;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filter\LocalizedToNormalized;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Checkout\Controller\Cart\Add
{
   /* overwrite default checkout cart action*/
    protected $productRepository;
   
    protected function _initProduct()
    {
        $result=[];
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
    public function execute()
    {
        $popup_block= $this->_objectManager->create('Magebees\Ajaxaddtocart\Block\Popup');
        $config=$popup_block->getConfig();
        
        if ($config['enable']==1) {
            $params = $this->getRequest()->getParams();
        
            try {
                if (isset($params['qty'])) {
                     $filter = new LocalizedToNormalized(
                         ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                     );
                    $params['qty'] = $filter->filter($params['qty']);
                }
        
                $product = $this->_initProduct();
                /** check whether display popup or not*/
                $productId = (int)$this->getRequest()->getParam('product');
            
                $related = $this->getRequest()->getParam('related_product');
                $upsell=$this->getRequest()->getParam('upsell_product');
                $crosssell=$this->getRequest()->getParam('crosssell_product');
            
                /**
             * Check product availability
             */
                if (!$product) {
                    return $this->goBack();
                }

                $this->cart->addProduct($product, $params);
            
                if (!empty($related)) {
                    $this->cart->addProductsByIds(explode(',', (string)$related));
                }
            
                $this->cart->save();

                /**
             * dispatch event [events.xml] after add to cart product and display confirmation content in popup
             */
                $this->_eventManager->dispatch(
                    'ajaxcheckout_cart_add_product_complete',
                    ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                );
                if ($this->getRequest()->isAjax()) {
                    return;
                }
            

                if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                    if (!$this->cart->getQuote()->getHasError()) {
                       /* $message = __(
                        'You added %1 to your shopping cart.',
                        $product->getName()
                        );
                        $this->messageManager->addSuccessMessage($message);*/
                    }
                        return $this->goBack(null, $product);
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if (preg_match('/requested/', $e->getMessage())) {
                    $result['popup_content'] = $e->getMessage();
                    $result['error'] = true;
                    return $this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));
                }
                $url = $this->_checkoutSession->getRedirectUrl(true);

                /* set below condition if in response product page url then display product content in popup */
                if ($url) {
                    $this->getResponse()->setRedirect($url.'?options=ajax');
                }
                if (!$url) {
                    $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                    $url = $this->_redirect->getRedirectUrl($cartUrl);
                }

                return $this->goBack($url);
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                return $this->goBack();
            }
        } else {
             return parent::execute();
        }
    }
    protected function goBack($backurl = null, $cartproduct = null)
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backurl);
        }

        $results = [];
        if ($backurl || $backurl = $this->getBackUrl()) {
            $results['backUrl'] = $backurl;
        } else {
            if ($cartproduct && !$cartproduct->getIsSalable()) {
                $results['product'] = [
                'statusText' => __('Out of stock')
                ];
            }
        }
        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($results)
        );
    }
}
