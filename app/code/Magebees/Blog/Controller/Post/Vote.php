<?php
namespace Magebees\Blog\Controller\Post;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Vote extends \Magento\Framework\App\Action\Action
{
	 
	public $_registry;
	protected $request;
	protected $_likedislike;
	protected $_remoteaddress;
	protected $resultPageFactory;
	protected $_bloghelper;
	protected $_storemanager;
	protected $_customerSession;
   	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magebees\Blog\Model\LikeDislike $likedislike,
		\Magebees\Blog\Helper\Data $bloghelper,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteaddress,
		\Magento\Store\Model\StoreManagerInterface $storemanager,
		\Magento\Customer\Model\Session $customerSession
    ) {
       $this->request = $request;
        parent::__construct($context);
        $this->_bloghelper = $bloghelper;
		$this->resultPageFactory = $resultPageFactory;
		$this->_likedislike = $likedislike;
		$this->_remoteaddress = $remoteaddress;
		$this->_storemanager = $storemanager;
		$this->_customerSession = $customerSession;
    }
    public function execute()
    {
		try {
			
			
			$data = $this->request->getParams();
			$remove_address = $this->_remoteaddress->getRemoteAddress();
			$post_id = $data['post_id'];
			$storeId = $this->_storemanager->getStore()->getId();
			if($this->_customerSession->isLoggedIn()):
				$customerId=$this->_customerSession->getCustomer()->getId();
				else:
				$customerId=0;
			endif;
			
			if(isset($data['post_id']))
			{	
				$post_id = $data['post_id'];
				$action = $data['action'];
				$vote_collection = $this->_likedislike->getCollection();
				
				$vote_collection->addFieldToFilter('post_id', array('eq' => $post_id));
				$vote_collection->addFieldToFilter('system_ip', array('eq' => $remove_address));
				$vote_collection->addFieldToFilter('store_id', array('eq' => $storeId));
				$vote_collection->addFieldToFilter('customer_id', array('eq' => $customerId));
				
				
				$like_dislike_id = $vote_collection->getFirstItem()->getLikeDislikeId();
				if($vote_collection->getFirstItem()->getLikeDislikeId()){
					if($action=='minus')
					{
						if($vote_collection->getFirstItem()->getPostdislike())
						{
						$vote_collection->getFirstItem()->delete();
						}else{
						$vote_collection->getFirstItem()->setPostlike(0);
						$vote_collection->getFirstItem()->setPostdislike(1);
						$vote_collection->getFirstItem()->save();
						}
						
						
					}else if($action=='plus'){
						
						if($vote_collection->getFirstItem()->getPostlike())
						{
						$vote_collection->getFirstItem()->delete();
						}else{
						$vote_collection->getFirstItem()->setPostlike(1);
						$vote_collection->getFirstItem()->setPostdislike(0);
						$vote_collection->getFirstItem()->save();
						}
					}
					
				}else{
				
				$data['store_id'] = $storeId;
				$data['customer_id'] = $customerId;
				$data['system_ip'] = $this->_remoteaddress->getRemoteAddress();
				if($action=='minus')
				{
					
					$data['postlike'] = 0;
					$data['postdislike'] = 1;
				}else if($action=='plus'){
					$data['postlike'] = 1;
					$data['postdislike'] = 0;
				}
				$this->_likedislike->setData($data);
				$this->_likedislike->save();
				}
				$result = array();
				$result['like_count'] = $this->getPostLikeCount($post_id);
				$result['dis_like_count'] = $this->getPostDisLikeCount($post_id);
				$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        		$resultJson->setData($result);
        		return $resultJson;
			}
		} catch (\Exception $e) {
			$this->messageManager->addError($e->getMessage());
        }
		
		
	}
		
		public function getPostLikeCount($post_id)
		{
		$storeId = $this->_storemanager->getStore()->getId();
		$vote_collection = $this->_likedislike->getCollection();
		$vote_collection->addFieldToFilter('store_id', array('eq' => $storeId));
		$vote_collection->addFieldToFilter('post_id', array('eq' => $post_id));
		$vote_collection->addFieldToFilter('postlike', array('eq' => 1));
		$vote_collection->addFieldToFilter('postdislike', array('eq' => 0));
		return $vote_collection->getSize();
		}
		public function getPostDisLikeCount($post_id)
		{
		$storeId = $this->_storemanager->getStore()->getId();
		$vote_collection = $this->_likedislike->getCollection();
		$vote_collection->addFieldToFilter('store_id', array('eq' => $storeId));
		$vote_collection->addFieldToFilter('post_id', array('eq' => $post_id));
		$vote_collection->addFieldToFilter('postlike', array('eq' => 0));
		$vote_collection->addFieldToFilter('postdislike', array('eq' => 1));
		return $vote_collection->getSize();
		}
		

}
