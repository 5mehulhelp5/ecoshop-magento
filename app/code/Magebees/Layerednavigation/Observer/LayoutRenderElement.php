<?php
namespace Magebees\Layerednavigation\Observer;

use Magento\Framework\Event\ObserverInterface;

class LayoutRenderElement implements ObserverInterface
{
    protected $pageConfig;
    protected $_requesthttp;
    public function __construct(   
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\App\Request\Http $requesthttp
        
    ) {    
        $this->pageConfig = $pageConfig; 
        $this->_requesthttp = $requesthttp;
    }

    /**
     * Array of all product list & navigation block names.
     *
     * @var array
     */
    private $blockNames = [
        'product_list' => [
           // 'category.products.list',
            'category.products',
            'search.result',
            'brandinfo'
        ],
        'navigation' => [
            'catalog.leftnav',
            'catalogsearch.leftnav',
        ],
    ];

    /**
     * Surrounds the content of the product lists & navigation blocks with span elements to find them
     * later in the DOM.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $actionName = $this->_requesthttp->getFullActionName();
        $moduleName = $this->_requesthttp->getModuleName();
        if($actionName == "catalog_category_view" || $actionName == "catalogsearch_result_index" || $moduleName == "layerednavigation"){

            $name = $observer->getElementName();
            foreach ($this->blockNames as $blockType => $blockNames) {
                if (in_array($name, $blockNames)) {
                    $this->addBeforeAndAfterTagToTransport($observer->getTransport(), $blockType, $name);
                    break;
                }
            }
        }    
    }

    /**
     * Surrounds the output of transport with span before and a span after tag.
     */
    private function addBeforeAndAfterTagToTransport(\Magento\Framework\DataObject $transport, $blockType, $blockName)
    {
        $actionName = $this->_requesthttp->getFullActionName();
        $moduleName = $this->_requesthttp->getModuleName();
        if($actionName == "catalog_category_view" || $actionName == "catalogsearch_result_index" || $moduleName == "layerednavigation"){
            $output = $transport->getData('output');
            $output = sprintf(
                '<span id="magebees_%1$s_before" data-block-name="%2$s"></span>%3$s<span id="magebees_%1$s_after">'
                    .'</span>',
                $blockType,
                urlencode($blockName),
                $output
            );
            $transport->setData('output', $output);
        }
    }
}
