<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebees\Ajaxsearch\Controller\Ajax;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Framework\Controller\ResultFactory;

class Suggest extends \Magento\Framework\App\Action\Action
{
    
    /**
     * Catalog session
     *
     * @var Session
     */
    protected $_catalogSession;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var QueryFactory
     */
    private $_queryFactory;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;
    
    protected $autocomplete;
    /**
     * @param Context $context
     * @param Session $catalogSession
     * @param StoreManagerInterface $storeManager
     * @param QueryFactory $queryFactory
     * @param Resolver $layerResolver
     */
    public function __construct(
        Context $context,
        AutocompleteInterface $autocomplete,
        Session $catalogSession,
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver
    ) {
        parent::__construct($context);
         $this->autocomplete = $autocomplete;
        $this->_storeManager = $storeManager;
        $this->_catalogSession = $catalogSession;
        $this->_queryFactory = $queryFactory;
        $this->layerResolver = $layerResolver;
    }

    /**
     * Display search result
     *
     * @return void
     */
    public function execute()
    {
        $search_block= $this->_objectManager->create('Magebees\Ajaxsearch\Block\Searchbox');
        $config=$search_block->getConfig();
        $recent_config=$search_block->getRecentConfig();
        $popular_config=$search_block->getPopularConfig();
        $q=$this->getRequest()->getParam('q');
        $searchresult_url =$search_block->getCatalogSearchUrl();
        if ($config['enable']==1) {
            $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);
        /* @var $query \Magento\Search\Model\Query */
            $query = $this->_queryFactory->get();

            $query->setStoreId($this->_storeManager->getStore()->getId());

            if ($query->getQueryText() != '') {
                if ($this->_objectManager->get('Magento\CatalogSearch\Helper\Data')->isMinQueryLength()) {
                    $query->setId(0)->setIsActive(1)->setIsProcessed(1);
                } else {
                    $query->saveIncrementalPopularity();
                }

                $this->_objectManager->get('Magento\CatalogSearch\Helper\Data')->checkNotes();
            }
        /* start for get product collection of query text */
        
            $resultFactory= $this->_objectManager->create('\Magento\Framework\View\Result\PageFactory');
            $resultPage= $resultFactory->create();
            $layoutblk = $resultPage->addHandle('ajaxsearch_result_index')->getLayout();
            $search_content= $layoutblk->getBlock('search_result_list')->toHtml();
        
        /*end for get product collection of query text */
        
            $result = [];
            $responseData = [];
                 
        /* for get suggested search text */
            $autocompleteData = $this->autocomplete->getItems();
            foreach ($autocompleteData as $resultItem) {
                $responseData[]= $resultItem->toArray();
            }
        
        /* for get recent search text */
        
            $recent_content= $layoutblk->getBlock('recent_search_result_list')->toHtml();
        
        /* for get update recent text */
        
            $update_recent= $layoutblk->getBlock('update_recent_result_list')->toHtml();
        
        /* for get popular search text */
        
            $popular_content= $layoutblk->getBlock('popular_search_result_list')->toHtml();
        
        /* for get category search text */
        
            $category_content= $layoutblk->getBlock('category_search_result_list')->toHtml();
        
        /* for get cms search text */
        
            $cms_content= $layoutblk->getBlock('cms_search_result_list')->toHtml();
        
            $result['products'] = $search_content;
            $result['suggest'] = $responseData;
            $result['url'] = $searchresult_url;
            $result['recent'] = $recent_content;
            $result['update_recent'] = $update_recent;
            $result['popular'] = $popular_content;
            $result['category'] = $category_content;
            $result['cms'] = $cms_content;
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($result);
            return $resultJson;
        } else {
            if (!$this->getRequest()->getParam('q', false)) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_url->getBaseUrl());
                return $resultRedirect;
            }

            $autocompleteData = $this->autocomplete->getItems();
            $responseData = [];
            foreach ($autocompleteData as $resultItem) {
                $responseData[] = $resultItem->toArray();
            }
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($responseData);
            return $resultJson;
        }
    }
}
