<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Ajaxaddtocart\Controller\Cart;

class Delete extends \Magento\Checkout\Controller\Cart\Delete
{
    /**
     * overwrite Delete shopping cart item action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    
    public function execute()
    {
        
        $popup_block= $this->_objectManager->create('Magebees\Ajaxaddtocart\Block\Popup');
        $config=$popup_block->getConfig();
        
        if ($config['enable']==1) {
            if (!$this->_formKeyValidator->validate($this->getRequest())) {
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }

            $id = (int)$this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $Quote= $this->_objectManager->create('Magento\Quote\Model\Quote\Item')->load($id);
                    $product_id=$Quote->getProductId();
                    $this->cart->removeItem($id)->save();
                /**
                 * dispatch event [events.xml] after remove product from cart and display confirmation content in popup
                 */
                    $this->_eventManager->dispatch(
                        'checkout_cart_delete_product_complete',
                        ['product' => $product_id, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('We can\'t remove the item.'));
                    $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                }
            }
        } else {
             return parent::execute();
        }
    }
}
