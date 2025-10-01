<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Ajaxaddtocart\Controller\Wishlist\Index;

class Cart extends \Magento\Wishlist\Controller\AbstractIndex
{
    protected $wishlistProvider;
    protected $quantityProcessor;
    protected $itemFactory;
    protected $cart;
    protected $cartHelper;
    private $optionFactory;
    protected $productHelper;
    protected $escaper;
    protected $helper;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Wishlist\Model\LocaleQuantityProcessor $quantityProcessor,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Wishlist\Model\Item\OptionFactory $optionFactory,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Wishlist\Helper\Data $helper,
        \Magento\Checkout\Helper\Cart $cartHelper
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->quantityProcessor = $quantityProcessor;
        $this->itemFactory = $itemFactory;
        $this->cart = $cart;
        $this->optionFactory = $optionFactory;
        $this->productHelper = $productHelper;
        $this->escaper = $escaper;
        $this->helper = $helper;
        $this->cartHelper = $cartHelper;
        parent::__construct($context);
    }

    /**
     * Add wishlist item to shopping cart
     */
    public function execute()
    {
        $post_data=$this->getRequest()->getPost()->toArray();
        $result=[];
        $popup_block= $this->_objectManager->create('Magebees\Ajaxaddtocart\Block\Popup');
        $config=$popup_block->getConfig();
        $itemId = (int)$this->getRequest()->getParam('item');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        /* @var $item \Magento\Wishlist\Model\Item */
        $item = $this->itemFactory->create()->load($itemId);
        if (!$item->getId()) {
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        }
        $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
        if (!$wishlist) {
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        }

        // Set qty
        if (isset($post_data['qty'])) {
             $quantity = $post_data['qty'];
        } else {
            $quantity = $this->getRequest()->getParam('qty');
        }
        if (is_array($quantity)) {
            if (isset($quantity[$itemId])) {
                $quantity = $quantity[$itemId];
            } else {
                $quantity = 1;
            }
        }
        $quantity = $this->quantityProcessor->process($quantity);
        if ($quantity) {
            $item->setQty($quantity);
        }

        $wishlistRedirectUrl = $this->_url->getUrl('*/*');
        
        $configureUrl = $this->_url->getUrl(
            '*/*/configure/',
            [
                'id' => $item->getId(),
                'product_id' => $item->getProductId(),
            ]
        );

        if ($config['enable']==1) {
            try {
                /** @var \Magento\Wishlist\Model\ResourceModel\Item\Option\Collection $options */
                $cart_options = $this->optionFactory->create()->getCollection()->addItemFilter([$itemId]);
                $item->setOptions($cart_options->getOptionsByItem($itemId));

                $wishlistBuyRequest = $this->productHelper->addParamsToBuyRequest(
                    $this->getRequest()->getParams(),
                    ['current_config' => $item->getBuyRequest()]
                );

                    $item->mergeBuyRequest($wishlistBuyRequest);
                    $item->addToCart($this->cart, true);
                    $this->cart->save()->getQuote()->collectTotals();
                    $wishlist->save();
            

                if (!$this->cart->getQuote()->getHasError()) {
                    if (!$this->getRequest()->isAjax()) {
                        $message = __(
                            'You added %1 to your shopping cart.',
                            $this->escaper->escapeHtml($item->getProduct()->getName())
                        );
                        $this->messageManager->addSuccess($message);
                    }
                
                
                     $config=$popup_block->getConfig();
        
                    if ($config['enable']==1) {
                        $this->_eventManager->dispatch(
                            'wishlist_cart_add_product_complete',
                            ['product' =>$item->getProduct(), 'request' => $this->getRequest(), 'response' => $this->getResponse(),'item'=>$itemId]
                        );
                        if ($this->getRequest()->isAjax()) {
                                   return;
                        }
                    }
                }
        
                if ($this->cartHelper->getShouldRedirectToCart()) {
                     $wishlistRedirectUrl = $this->cartHelper->getCartUrl();
                } else {
                       $refererUrl = $this->_redirect->getRefererUrl();
                    if ($refererUrl && $refererUrl != $configureUrl) {
                        $wishlistRedirectUrl = $refererUrl;
                    }
                }
            } catch (\Magento\Catalog\Model\Product\Exception $e) {
                $this->messageManager->addError(__('This product(s) is out of stock.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->getRequest()->isAjax()) {
                    if (preg_match('/requested/', $e->getMessage())) {
                        $result['popup_content'] = $e->getMessage();
                        $result['error'] = true;
                        return $this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));
                    }
                }
                $this->messageManager->addNotice($e->getMessage());
                $wishlistRedirectUrl = $configureUrl;
            } catch (\Exception $e) {
               /* $this->messageManager->addException($e, __('We can\'t add the item to the cart right now.'));*/
            }
            $this->helper->calculate();
            $resultRedirect->setUrl($wishlistRedirectUrl);
            return $resultRedirect;
        } else {
            try {
                $cart_options = $this->optionFactory->create()->getCollection()->addItemFilter([$itemId]);
                $item->setOptions($cart_options->getOptionsByItem($itemId));

                $wishlistBuyRequest = $this->productHelper->addParamsToBuyRequest(
                    $this->getRequest()->getParams(),
                    ['current_config' => $item->getBuyRequest()]
                );

                $item->mergeBuyRequest($wishlistBuyRequest);
                $item->addToCart($this->cart, true);
                $this->cart->save()->getQuote()->collectTotals();
                $wishlist->save();

                if (!$this->cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $this->escaper->escapeHtml($item->getProduct()->getName())
                    );
                      $this->messageManager->addSuccess($message);
                }

                if ($this->cartHelper->getShouldRedirectToCart()) {
                    $wishlistRedirectUrl = $this->cartHelper->getCartUrl();
                } else {
                    $refererUrl = $this->_redirect->getRefererUrl();
                    if ($refererUrl && $refererUrl != $configureUrl) {
                        $wishlistRedirectUrl = $refererUrl;
                    }
                }
            } catch (\Magento\Catalog\Model\Product\Exception $e) {
                $this->messageManager->addError(__('This product(s) is out of stock.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addNotice($e->getMessage());
                $wishlistRedirectUrl = $configureUrl;
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t add the item to the cart right now.'));
            }

            $this->helper->calculate();

            if ($this->getRequest()->isAjax()) {
                /** @var \Magento\Framework\Controller\Result\Json $resultJson */
                $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
                $resultJson->setData(['backUrl' => $wishlistRedirectUrl]);
                return $resultJson;
            }
        
            $resultRedirect->setUrl($wishlistRedirectUrl);
            return $resultRedirect;
        }
    }
}
