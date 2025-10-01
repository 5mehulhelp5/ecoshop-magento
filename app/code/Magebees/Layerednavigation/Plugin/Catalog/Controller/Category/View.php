<?php
namespace Magebees\Layerednavigation\Plugin\Catalog\Controller\Category;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;

class View
{
    protected $resultFactory;
	protected $request;

    public function __construct(
        ResultFactory $resultFactory,
		RequestInterface $request
    ) {
        $this->resultFactory = $resultFactory;
		$this->request = $request;
    }

    public function afterExecute($subject, $result)
    {
		
        if ($subject->getRequest()->getParam('left_layerednavigation')) {
            $layout = $result->getLayout();
            $layeredNavigationHtml = $layout->getBlock('catalog.leftnav')->toHtml();
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $resultPage->setContents($layeredNavigationHtml);
            return $resultPage;
        }
		
		if ($subject->getRequest()->getParam('top_layerednavigation')) {
            $layout = $result->getLayout();
            $layeredNavigationHtmlTop = $layout->getBlock('catalog.leftnav1')->toHtml();
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_RAW);
            $resultPage->setContents($layeredNavigationHtmlTop);
            return $resultPage;
        }
		
        return $result;	
    }
}