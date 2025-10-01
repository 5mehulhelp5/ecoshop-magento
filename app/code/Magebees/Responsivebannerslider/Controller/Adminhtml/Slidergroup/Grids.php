<?php
namespace Magebees\Responsivebannerslider\Controller\Adminhtml\Slidergroup;
class Grids extends \Magento\Backend\App\Action
{
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }
	public function execute()
	{  
		$resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->
		getBlock('responsivebannerslider_slidergroup_edit_tab_product')
            ->setProductsSlider($this->getRequest()->getPost('products_slider', null));
        return $resultLayout;
	}
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Responsivebannerslider::Heading');
    }
}
