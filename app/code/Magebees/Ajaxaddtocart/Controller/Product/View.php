<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Ajaxaddtocart\Controller\Product;

class View extends \Magento\Catalog\Controller\Product\View
{
    protected function noProductRedirect()
    {
        $store = $this->getRequest()->getQuery('store');
        if (isset($store) && !$this->getResponse()->isRedirect()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('');
        } elseif (!$this->getResponse()->isRedirect()) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }

    /**
     * overwrite Product view action for display product option detail in popup
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::execute();
        }
        $result=[];
        $popup_block= $this->_objectManager->create('Magebees\Ajaxaddtocart\Block\Popup');
        $config=$popup_block->getConfig();
        
        if ($config['enable']==1) {
        // Get initial data from request
            
            $categoryId = (int) $this->getRequest()->getParam('category', false);
            $productId = (int) $this->getRequest()->getParam('id');
            $specifyOptions = $this->getRequest()->getParam('options');
            /**start for fix issue of bundle product add to cart in compare page*/
            $params=$this->getRequest()->getParams();
            $compare_param=array_key_exists("startcustomization", $params);
            /**/
            if (($specifyOptions=='ajax') || ($compare_param)) {
                $product = $this->_initProduct();
                 $_response= $this->_objectManager->create('Magebees\Ajaxaddtocart\Model\Productcontent');
                $page_result=$_response->addProductOptionsBlock();
                $result['html_popup'] = $page_result;
                return $this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));
            }
            if (($this->getRequest()->isAjax()) && ($specifyOptions=='quote')) {
                $this->getResponse()->representJson(
                    $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode([
                        'backUrl' => $this->_redirect->getRedirectUrl()
                    ])
                );
                return;
            }
            if ($this->getRequest()->isPost() && $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
                $product = $this->_initProduct();
                if (!$product) {
                    return $this->noProductRedirect();
                }
                /** change for call extension controller and layout for popup***/
                        
                if ($this->getRequest()->isAjax()) {
                     $_response= $this->_objectManager->create('Magebees\Ajaxaddtocart\Model\Productcontent');
                    $page_result=$_response->addProductOptionsBlock();
                    $result['html_popup'] = $page_result;
                    return $this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));
                }
                $ajaxResultRedirect = $this->resultRedirectFactory->create();
                $ajaxResultRedirect->setRefererOrBaseUrl();
                return $ajaxResultRedirect;
            }

            // Prepare helper and params
            $params = new \Magento\Framework\DataObject();
            $params->setCategoryId($categoryId);
            $params->setSpecifyOptions($specifyOptions);

            // Render page
            try {
                $page = $this->resultPageFactory->create(false, ['isIsolated' => true]);
                $this->viewHelper->prepareAndRender($page, $productId, $this, $params);
                return $page;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                return $this->noProductRedirect();
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $resultForward = $this->resultForwardFactory->create();
                $resultForward->forward('noroute');
                return $resultForward;
            }
        } else {
             return parent::execute();
        }
    }
}
