<?php
namespace Magebees\Blog\Observer;
use Magento\Framework\Event\ObserverInterface;
class Urlrewriteremove implements ObserverInterface
{
	 protected $_categoryFactory;
	protected $_productFactory;
	protected $_pageFactory;
	protected $messageManager;
	protected $related_ids;
	protected $_blogUrlRewriteFactory;
	public function __construct( 
		\Magebees\Blog\Model\UrlRewriteFactory $blogUrlRewritefactory,
		\Magento\Framework\Message\ManagerInterface $managerInterface
	) {    
    $this->_blogUrlRewriteFactory = $blogUrlRewritefactory;
	$this->messageManager = $managerInterface;
	}
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       
		$data = $observer->getData('data');
		$identifier = $data['identifier'];
		if(isset($data['identifier']))
		{
				
			$related_rewrite_url_ids = $this->removeUrlsFromRewrite($identifier);
			$blog_url_delete = $this->_blogUrlRewriteFactory->Create()->getCollection()
						->addFieldToFilter('url_id', array('in' => $related_rewrite_url_ids));
			$blog_url_delete->walk('delete');
		}
		return $this;
    }
	public function removeUrlsFromRewrite($identifier){
		$blog_url_delete = $this->_blogUrlRewriteFactory->Create()->getCollection()
						->addFieldToFilter('new_url', array('eq' => $identifier));
		
		if($blog_url_delete->getFirstItem()->getUrlId()):
			$old_url = $blog_url_delete->getFirstItem()->getOldUrl();
			$this->related_ids[] =  $blog_url_delete->getFirstItem()->getUrlId();
			return $this->removeUrlsFromRewrite($old_url);
		endif;
		return $this->related_ids;
		
		
		
		
	
	}
	

}
