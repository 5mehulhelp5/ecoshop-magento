<?php
namespace Magebees\Blog\Controller\Adminhtml\Import;
use Magento\Framework\Controller\ResultFactory;
class Comment extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	protected $post;
	protected $category;
	protected $tag;
	protected $comment;
	protected $connectionFactory = null;
	protected $session;
    protected $_logger;
	protected $_timezoneInterface;
	protected $customergroup;
	protected $authSession;
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\ResourceConnection\ConnectionFactory $connectionFactory,
		\Magebees\Blog\Model\PostFactory $post,
		\Magebees\Blog\Model\CategoryFactory $category,
		\Magebees\Blog\Model\TagFactory $tag,
		\Magebees\Blog\Model\CommentFactory $comment,		
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magebees\Blog\Block\Logger $logger,
		\Magento\Customer\Model\Group $customergroup,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Magento\Backend\Model\Auth\Session $authSession
    ) {
    
        parent::__construct($context);
		$this->_scopeConfig = $scopeConfig;
		$this->post = $post;
		$this->category = $category;
		$this->tag = $tag;
		$this->comment = $comment;
		$this->connectionFactory = $connectionFactory;
		$this->session = $session;
		$this->_logger = $logger;
		$this->_timezoneInterface = $timezoneInterface;
		$this->customergroup = $customergroup;
		$this->authSession = $authSession;
		
    }
    public function execute()
    {
		 
			
		$currentDate = $this->_timezoneInterface->date()->format('Y-m-d H:i:s'); 
		$params = $this->getRequest()->getParams();
		
		$response = array();
		try {
			
			$host = $params['db_host'];
			$db_prefix = $params['db_prefix'];
			$db_name = $params['db_name'];
			$db_password = $params['db_password'];
			$db_username = $params['db_user'];
			$blog_connection = mysqli_connect($host,$db_username,$db_password,$db_name);
			$selectedStores = $params['selectedStores'];
			if (mysqli_connect_errno())
			  {
				$response['error'] = "Failed to connect to MySQL: " . mysqli_connect_error();;
			  
			  }
			
			$this->session->start();
			mysqli_set_charset($blog_connection, "utf8");
    	   	$_pref = mysqli_real_escape_string($blog_connection, $db_prefix);
				
			$wp_post_count = $this->session->getWPBlogPostCount();
			$wp_post_ids = $this->session->getWPBlogPostIds();
			$current_count = $params['current_count'];
			$total_posts = $wp_post_count;
			if(isset($wp_post_ids[$current_count]['ID']))
			{
				$wp_post_id = $wp_post_ids[$current_count]['ID'];
				$posts = $this->post->Create()->getCollection();
				$post_name = null;  	 
        		$posts->addFieldToFilter('is_imported', array('eq' => true));
				$posts->addFieldToFilter('term_id', array('eq' => $wp_post_id));
        		if ($posts->getFirstItem()->getPostId()):
					$current_post_id = $posts->getFirstItem()->getPostId();
				
			
			  /* find post comment s*/
				//$wordpress_posts_comments = 'SELECT * FROM '.$_pref.'comments WHERE `comment_approved`=1 AND `comment_post_ID` = ' . $wp_post_id;
				$wordpress_posts_comments = 'SELECT * FROM '.$_pref.'comments WHERE `comment_post_ID` = ' . $wp_post_id;
				$post_comment_count=0;
				$wordpress_blog_comments = array();	 
				if ($result = mysqli_query($blog_connection, $wordpress_posts_comments)) {
				 	while ($_commentsInfo = mysqli_fetch_assoc($result)) {
						$post_comment_count++;
						foreach (['comment_ID','comment_post_ID','comment_author','comment_author_email','comment_date','comment_date_gmt','comment_content','comment_approved','comment_parent'] as $key) {
                			$_commentsInfo[$key] = mb_convert_encoding($_commentsInfo[$key], 'HTML-ENTITIES', 'UTF-8');
							
            			}
						$wp_comment_id = $_commentsInfo['comment_ID'];
						
						$commentscheckexists = $this->comment->Create()->getCollection();
							$commentscheckexists->addFieldToFilter('is_imported', array('eq' => true));
							$commentscheckexists->addFieldToFilter('wp_comment_id', array('eq' => $wp_comment_id));
							if ($commentscheckexists->getFirstItem()->getCommentId()):
								$commentsexist_id = $commentscheckexists->getFirstItem()->getCommentId();
								$this->_logger->info('Wordpress Comment Id:: '.$wp_comment_id .' Already Exists, So We Skipped it');
								continue;
							endif;
						
						$wordpress_blog_comments[] = $_commentsInfo;	
						
						$commentParent_id = $_commentsInfo['comment_parent'];
						$parent_comment_id = false;
						if($commentParent_id)
						{
							
							$comments = $this->comment->Create()->getCollection();
							$comments->addFieldToFilter('is_imported', array('eq' => true));
							$comments->addFieldToFilter('wp_comment_id', array('eq' => $commentParent_id));
							if ($comments->getFirstItem()->getCommentId()):
								$parent_comment_id = $comments->getFirstItem()->getCommentId();
							endif;
							
							
						}
						$commentdata = array();
						$comment_date_gmt = $_commentsInfo['comment_date_gmt'];
						$comment_date_gmt = $this->_timezoneInterface->date(new \DateTime($comment_date_gmt))->format('Y-m-d H:i:s');
						if($_commentsInfo['comment_approved'])
						{
							$comment_status = true;
						}else{
							$comment_status = false;
						} 
						if($_commentsInfo['comment_author']=='admin')
						{
							$current_user = $this->authSession->getUser();
    						$admin_id = $current_user->getId();
							$author_type = 'admin';
						}else{
							$admin_id = false;
							$author_type = 'guest';
						} 
						$commentdata['parent_id'] = $parent_comment_id;
						$commentdata['post_id'] = $current_post_id;
						$commentdata['customer_id'] = null;
						$commentdata['admin_id'] = $admin_id;
						$commentdata['status'] = $comment_status;
						$commentdata['author_type'] = $author_type;
						$commentdata['author_nickname'] = $_commentsInfo['comment_author'];
						$commentdata['author_email'] = $_commentsInfo['comment_author_email'];
						$commentdata['text'] = $_commentsInfo['comment_content'];
						$commentdata['creation_time'] = $comment_date_gmt;
						$commentdata['update_time'] = $comment_date_gmt;
						$commentdata['is_imported'] = true;
						$commentdata['wp_comment_id'] = $_commentsInfo['comment_ID'];
						
						$commentCreate = $this->comment->Create();
						$commentCreate->setData($commentdata);
						$commentCreate->save();
						
					}
					
					
				$response['post_comment_count'] = $post_comment_count;			
				}
				endif;
			
			$response['success'] = true;		
			}
			
			
			}
			catch (\Magento\Framework\Model\Exception $e) 
			{
				$this->messageManager->addError(__($e->getMessage()));
				$response['message'] = $e->getMessage();
				$response['success'] = false;
				$response['error'] = true;
			}
			$response['wordpress_blog_post_count'] = $wp_post_count;
			
		
			if($wp_post_count==($current_count))
			{
			$response['next'] = false;
			}else{
			$response['next'] = true;
			$response['current_count'] = $current_count;
			}
			
			
		
		 $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response);
        return $resultJson;
	}
	protected function wordpressOutoutWrap( $pee, $br = true )
    {
        $pre_tags = array();

        if ( trim( $pee ) === '' ) {
            return '';
        }

        // Just to make things a little easier, pad the end.
        $pee = $pee . "\n";

        /*
         * Pre tags shouldn't be touched by autop.
         * Replace pre tags with placeholders and bring them back after autop.
         */
        if ( strpos( $pee, '<pre' ) !== false ) {
            $pee_parts = explode( '</pre>', (string)$pee );
            $last_pee  = array_pop( $pee_parts );
            $pee       = '';
            $i         = 0;

            foreach ( $pee_parts as $pee_part ) {
                $start = strpos( $pee_part, '<pre' );

                // Malformed html?
                if ( $start === false ) {
                    $pee .= $pee_part;
                    continue;
                }

                $name              = "<pre wp-pre-tag-$i></pre>";
                $pre_tags[ $name ] = substr( $pee_part, $start ) . '</pre>';

                $pee .= substr( $pee_part, 0, $start ) . $name;
                $i++;
            }

            $pee .= $last_pee;
        }
        // Change multiple <br>s into two line breaks, which will turn into paragraphs.
        $pee = preg_replace( '|<br\s*/?>\s*<br\s*/?>|', "\n\n", $pee );

        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';

        // Add a double line break above block-level opening tags.
        $pee = preg_replace( '!(<' . $allblocks . '[\s/>])!', "\n\n$1", $pee );

        // Add a double line break below block-level closing tags.
        $pee = preg_replace( '!(</' . $allblocks . '>)!', "$1\n\n", $pee );

        // Standardize newline characters to "\n".
        $pee = str_replace( array( "\r\n", "\r" ), "\n", $pee );

        // Collapse line breaks before and after <option> elements so they don't get autop'd.
        if ( strpos( $pee, '<option' ) !== false ) {
            $pee = preg_replace( '|\s*<option|', '<option', $pee );
            $pee = preg_replace( '|</option>\s*|', '</option>', $pee );
        }

        /*
         * Collapse line breaks inside <object> elements, before <param> and <embed> elements
         * so they don't get autop'd.
         */
        if ( strpos( $pee, '</object>' ) !== false ) {
            $pee = preg_replace( '|(<object[^>]*>)\s*|', '$1', $pee );
            $pee = preg_replace( '|\s*</object>|', '</object>', $pee );
            $pee = preg_replace( '%\s*(</?(?:param|embed)[^>]*>)\s*%', '$1', $pee );
        }

        /*
         * Collapse line breaks inside <audio> and <video> elements,
         * before and after <source> and <track> elements.
         */
        if ( strpos( $pee, '<source' ) !== false || strpos( $pee, '<track' ) !== false ) {
            $pee = preg_replace( '%([<\[](?:audio|video)[^>\]]*[>\]])\s*%', '$1', $pee );
            $pee = preg_replace( '%\s*([<\[]/(?:audio|video)[>\]])%', '$1', $pee );
            $pee = preg_replace( '%\s*(<(?:source|track)[^>]*>)\s*%', '$1', $pee );
        }

        // Collapse line breaks before and after <figcaption> elements.
        if ( strpos( $pee, '<figcaption' ) !== false ) {
            $pee = preg_replace( '|\s*(<figcaption[^>]*>)|', '$1', $pee );
            $pee = preg_replace( '|</figcaption>\s*|', '</figcaption>', $pee );
        }

        // Remove more than two contiguous line breaks.
        $pee = preg_replace( "/\n\n+/", "\n\n", $pee );

        // Split up the contents into an array of strings, separated by double line breaks.
        $pees = preg_split( '/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY );

        // Reset $pee prior to rebuilding.
        $pee = '';

        // Rebuild the content as a string, wrapping every bit with a <p>.
        foreach ( $pees as $tinkle ) {
            $pee .= '<p>' . trim( $tinkle, "\n" ) . "</p>\n";
        }

        // Under certain strange conditions it could create a P of entirely whitespace.
        $pee = preg_replace( '|<p>\s*</p>|', '', $pee );

        // Add a closing <p> inside <div>, <address>, or <form> tag if missing.
        $pee = preg_replace( '!<p>([^<]+)</(div|address|form)>!', '<p>$1</p></$2>', $pee );

        // If an opening or closing block element tag is wrapped in a <p>, unwrap it.
        $pee = preg_replace( '!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', '$1', $pee );

        // In some cases <li> may get wrapped in <p>, fix them.
        $pee = preg_replace( '|<p>(<li.+?)</p>|', '$1', $pee );

        // If a <blockquote> is wrapped with a <p>, move it inside the <blockquote>.
        $pee = preg_replace( '|<p><blockquote([^>]*)>|i', '<blockquote$1><p>', $pee );
        $pee = str_replace( '</blockquote></p>', '</p></blockquote>', $pee );

        // If an opening or closing block element tag is preceded by an opening <p> tag, remove it.
        $pee = preg_replace( '!<p>\s*(</?' . $allblocks . '[^>]*>)!', '$1', $pee );

        // If an opening or closing block element tag is followed by a closing <p> tag, remove it.
        $pee = preg_replace( '!(</?' . $allblocks . '[^>]*>)\s*</p>!', '$1', $pee );

        // Optionally insert line breaks.
        if ( $br ) {
            // Replace newlines that shouldn't be touched with a placeholder.
            // Normalize <br>
            $pee = str_replace( array( '<br>', '<br/>' ), '<br />', $pee );

            // Replace any new line characters that aren't preceded by a <br /> with a <br />.
            $pee = preg_replace( '|(?<!<br />)\s*\n|', "<br />\n", $pee );

            // Replace newline placeholders with newlines.
            $pee = str_replace( '<WPPreserveNewline />', "\n", $pee );
        }

        // If a <br /> tag is after an opening or closing block tag, remove it.
        $pee = preg_replace( '!(</?' . $allblocks . '[^>]*>)\s*<br />!', '$1', $pee );

        // If a <br /> tag is before a subset of opening or closing block tags, remove it.
        $pee = preg_replace( '!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee );
        $pee = preg_replace( "|\n</p>$|", '</p>', $pee );

        // Replace placeholder <pre> tags with their original content.
        if ( ! empty( $pre_tags ) ) {
            $pee = str_replace( array_keys( $pre_tags ), array_values( $pre_tags ), $pee );
        }

        // Restore newlines in all elements.
        if ( false !== strpos( $pee, '<!-- wpnl -->' ) ) {
            $pee = str_replace( array( ' <!-- wpnl --> ', '<!-- wpnl -->' ), "\n", $pee );
        }

        return $pee;
    }
    protected function _isAllowed()
    {
        return true;
    }
}
