<?php
namespace Magebees\Finder\Controller\Adminhtml\Import;

use Magento\Framework\App\Filesystem\DirectoryList;

class Deleteold extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
    protected $resource;
    protected $connection;
 
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        parent::__construct($context);
    }
 
    public function execute() {
		$result = array();
        $post = $this->getRequest()->getPost()->toArray();
		
		$dropdownsCol = $this->_objectManager->create('Magebees\Finder\Model\Dropdowns')->getCollection();
        $dropdownsCol->addFieldToFilter('finder_id', $post['finder_id']);
        $dropdown_ids = $dropdownsCol->load()->getColumnValues('dropdown_id');
		
		/**************Delete Existing records if user selected "YES" *************************/
		if (isset($post['delete_old']) && $post['delete_old']==1) {
			$deleteCollection = $this->_objectManager->create('Magebees\Finder\Model\Ymmvalue')->getCollection()->addFieldToFilter('dropdown_id', ['in'=>[$dropdown_ids]]);
			$deleteCollection->setPageSize(500)->walk('delete');
			if($deleteCollection->getSize()){
				$result['delete_old'] = 1;
			}else{
				$result['delete_old'] = 0;
			}
			
			$this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));
		}
		
    }
    
	
	
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Finder::finder_content');
    }
}
