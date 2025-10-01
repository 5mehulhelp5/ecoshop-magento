<?php
namespace Magebees\Finder\Controller\Adminhtml\Product;

class Delete extends \Magento\Backend\App\Action
{
    protected $_productsFactory;
	protected $_finderFactory;
    protected $_dropdownsFactory;
    protected $_mapvalueFactory;
    protected $_ymmvalueFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magebees\Finder\Model\FinderFactory $finderFactory,
        \Magebees\Finder\Model\DropdownsFactory $dropdownsFactory,
        \Magebees\Finder\Model\MapvalueFactory $mapvalueFactory,
        \Magebees\Finder\Model\YmmvalueFactory $ymmvalueFactory
    ) {
        parent::__construct($context);
        $this->_finderFactory = $finderFactory;
        $this->_dropdownsFactory = $dropdownsFactory;
        $this->_mapvalueFactory = $mapvalueFactory;
        $this->_ymmvalueFactory = $ymmvalueFactory;
    }

    public function execute()
    {
        $map_id = $this->getRequest()->getParam('id');
        if ($map_id) {
            try {
                $mapModel = $this->_mapvalueFactory->create()->load($map_id);
                $ymm_value_id = $mapModel->getYmmValueId();
                $mapModel->delete();
                $finderModel = $this->_finderFactory->create();
                $currentId = $ymm_value_id;
                $ymmvalueModel = $this->_ymmvalueFactory->create()->load($ymm_value_id);
                $dropdownId =  $ymmvalueModel->getDropdownId();
                while ($ymm_value_id && $finderModel->isDeleteYmmData($ymm_value_id)) {
                    $ymmvalueModel = $this->_ymmvalueFactory->create()->load($ymm_value_id);
                    $ymm_value_id = $ymmvalueModel->getParentId();
                    $dropdownId =  $ymmvalueModel->getDropdownId();
                    $ymmvalueModel->delete();
                }
                $dropdown =  $this->_dropdownsFactory->create()->load($dropdownId);
                $finderId = $dropdown->getFinderId();
                $this->messageManager->addSuccess(__('Product Deleted successfully !'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/finder/edit', ['id' => $finderId]);
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
}
