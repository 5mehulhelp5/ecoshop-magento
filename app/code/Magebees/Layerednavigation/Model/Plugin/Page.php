<?php
namespace Magebees\Layerednavigation\Model\Plugin;

class Page
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;
    protected $pageConfig;
    protected $_helper;

    /**
     * Creates an instance of a Page plugin.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magebees\Layerednavigation\Helper\Data $helper)
    {
        $this->request = $context->getRequest();
        $this->pageConfig =$context->getPageConfig();
         $this->_helper = $helper;
        $this->eventManager = $context->getEventManager();
    }

    /**
     * If parameter magebeesAjax is given in the request, function render should only return a json with block
     * category.products.list and catalog.leftnav.
     */
     
    public function aroundRenderResult(
        \Magento\Framework\View\Result\Page $subject,
        \Closure $proceed,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        if ($this->request->getParam('magebeesAjax')) {
            $data=[];
            \Magento\Framework\Profiler::start('LAYOUT');
            \Magento\Framework\Profiler::start('layout_render');

            /** @var Magento\Framework\View\Layout $layout */
            $layout = $subject->getLayout();
            $prodListBlockParam = urldecode($this->request->getParam('productListBlock'));
            $navBlockParam = urldecode($this->request->getParam('navigationBlock'));
            $productListBlock = $layout->getBlock($prodListBlockParam);
            $leftnavBlock = $layout->getBlock($navBlockParam);
            //$breadcrumbs= $layout->getBlock('breadcrumbs');
            //print_r($breadcrumbs->toHtml());die;
            $parameters = [
                '&magebeesAjax=1',
                '&productListBlock='.$prodListBlockParam,
                '&navigationBlock='.$navBlockParam,
            ];
            $pagetitle=$this->pageConfig->getTitle()->get();
            $data['list_product']=$this->removeUriParameters($productListBlock->toHtml(), $parameters);
			
            $data['left_nav_content']=$this->removeUriParameters($leftnavBlock->toHtml(), $parameters);			
            if ($layout->getBlock('catalog.leftnav1')) {
                $data['top_nav_content']=$this->removeUriParameters($layout->getBlock('catalog.leftnav1')->toHtml(), $parameters);
            }
            
            $data['title']=$pagetitle;
            $response->appendBody(json_encode($data));
            $this->eventManager->dispatch('layout_render_before');
            $this->eventManager->dispatch('layout_render_before_' . $this->request->getFullActionName());
            \Magento\Framework\Profiler::stop('layout_render');
            \Magento\Framework\Profiler::stop('LAYOUT');

            return $subject;
        }

        return $proceed($response);
    }

    /**
     * Replaces the parameters in the html with an empty string.
     *
     * @param string $blockName
     * @param array  $parameters
     */
    private function removeUriParameters($html, array $parameters)
    {
        
        $search = [];
        $search_char = [];
        foreach ($parameters as $parameter) {
            $search[] = $parameter;
            $search_char[] = htmlspecialchars($parameter);
        }
        $url_suffix=$this->_helper->getUrlSuffix();

        $html=trim(str_replace($search,'', $html));
         $html=trim(str_replace($search_char,'', $html));
		$test=htmlspecialchars('?magebeesAjax=1&');
		$html=trim(str_replace($test,'?',$html));
		$test1=htmlspecialchars('?magebeesAjax=1');
		$html=trim(str_replace($test1,'',$html));
		$test2=htmlspecialchars('&magebeesAjax=1&');
		$html=trim(str_replace($test2,'',$html));
        if($url_suffix)
        {
            $html=trim(str_replace($url_suffix.'&',$url_suffix.'?',$html));
        }		 
		return $html;
		
    }
}
