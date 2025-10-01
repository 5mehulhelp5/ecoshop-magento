<?php namespace Magebees\Testimonial\Controller\Index;

use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Refresh extends Action
{

    protected $_helper;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magebees\Testimonial\Helper\Data $helper)
    {
        parent::__construct($context);
        $this->_helper = $helper;
    }

    public function execute()
    {

        $image_name = $this->_helper->createCaptchaImage();
        $resultFactory= $this->_objectManager->create('\Magento\Framework\View\Result\PageFactory');
        $resultPage= $resultFactory->create();
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($image_name);
        return $resultJson;
    }
}
