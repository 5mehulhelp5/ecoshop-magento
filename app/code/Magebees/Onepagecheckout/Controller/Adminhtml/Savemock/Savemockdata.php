<?php
namespace Magebees\Onepagecheckout\Controller\Adminhtml\Savemock;
use Magento\Framework\App\Filesystem\DirectoryList;
class Savemockdata extends \Magento\Backend\App\Action
{
	protected $_coreRegistry = null;
	protected $resultPageFactory;
	protected $_transactionFactory;
	
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\DB\Transaction $transactionFactory
    ) {
		$this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_transactionFactory = $transactionFactory;       
		parent::__construct($context);
    }
	
	public function execute()
    {
		$id = $this->getRequest()->getParam('id');
        $mockdata = $this->getRequest()->getParam('mockdata');
		$deleteTransaction = $this->_transactionFactory;
		$saveTransaction = $this->_transactionFactory;
		$dataObject = $this->_objectManager->create('Magebees\Onepagecheckout\Model\Successcustom');
		$dataObject ->setOscsection($id)->setOscfieldname($mockdata);
		$oldPath = $this->_objectManager->create('Magebees\Onepagecheckout\Model\Successcustom')->getCollection()->addFieldToFilter('oscfieldname', $mockdata)->getFirstItem();
		if ($oldPath) { 
			$dataObject->setId($oldPath->getEntityId()); 
		}
        $inherit = !empty($data['inherit']);
        if (!$inherit) {
            $saveTransaction->addObject($dataObject);
        } else {
            $deleteTransaction->addObject($dataObject);
        }
        $saveTransaction->save();	
    }
	protected function _isAllowed()
    {
        return true;
    }
}
