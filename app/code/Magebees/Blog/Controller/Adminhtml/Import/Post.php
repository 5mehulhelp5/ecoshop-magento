<?php
namespace Magebees\Blog\Controller\Adminhtml\Import;
use Magento\Framework\Controller\ResultFactory;
class Post extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_coreSession;
	protected $_scopeConfig;
	protected $connectionFactory = null;
	protected $session;
    protected $_logger;
	protected $authSession;
	protected $post;
	protected $category;
	protected $tag;
	protected $_timezoneInterface;
	protected $customergroup;
	
	
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\ResourceConnection\ConnectionFactory $connectionFactory,
		\Magebees\Blog\Model\PostFactory $post,
		\Magebees\Blog\Model\CategoryFactory $category,
		\Magebees\Blog\Model\TagFactory $tag,
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
				$wordpress_posts = 'SELECT * FROM '.$_pref.'posts WHERE post_type = "post" AND ID = "'.$wp_post_id.'" ORDER BY ID ASC';
				$wordpress_blog_posts_info = array();	
				if ($result = mysqli_query($blog_connection, $wordpress_posts)) {
				 	while ($_postinfo = mysqli_fetch_assoc($result)) {
						foreach (['ID','post_date','post_date_gmt','post_content','post_title','post_excerpt',
								  'post_status','post_name','post_modified','post_modified_gmt','comment_count'] as $key) {
                			$_postinfo[$key] = mb_convert_encoding($_postinfo[$key], 'HTML-ENTITIES', 'UTF-8');
							
            			}	
					  	$wordpress_blog_posts_info = $_postinfo;
					}
					
					
				}
				
				$wordpress_posts_categories_term_ids = 'SELECT tt.term_id as term_id FROM '.$_pref.'term_relationships tr LEFT JOIN '.$_pref.'term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tr.`object_id` = "'.$wp_post_id.'" AND tt.taxonomy = "category"';
				
				$wordpress_blog_posts_old_catids = array();	
				if ($result = mysqli_query($blog_connection, $wordpress_posts_categories_term_ids)) {
				 	while ($_termids = mysqli_fetch_assoc($result)) {
						foreach (['term_id'] as $key) {
                			$_wp_term_id = mb_convert_encoding($_termids[$key], 'HTML-ENTITIES', 'UTF-8');
							$wordpress_blog_posts_old_catids[] = $_wp_term_id;	
            			}	
					  	
					}
					
					
				}
				
				$post_magento_cat_ids = array();
				if($wordpress_blog_posts_old_catids > 0)
				{ 
					
					$blog_categories = $this->category->Create()->getCollection()->addFieldToSelect('category_id');
        			$blog_categories->addFieldToFilter('term_id', array('in' => $wordpress_blog_posts_old_catids));
					foreach($blog_categories as $key => $category):
					if(isset($category['category_id']))
					{
					$post_magento_cat_ids[]  = $category['category_id'];
					}
					endforeach;
					
				}
				
				
				
				$wordpress_posts_tags_term_ids = 'SELECT tt.term_id as term_id FROM '.$_pref.'term_relationships tr LEFT JOIN '.$_pref.'term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tr.`object_id` = "'.$wp_post_id.'" AND tt.taxonomy = "post_tag"';
				
				$wordpress_blog_posts_old_tagids = array();	
				if ($result = mysqli_query($blog_connection, $wordpress_posts_tags_term_ids)) {
				 	while ($_termids = mysqli_fetch_assoc($result)) {
						foreach (['term_id'] as $key) {
                			//$_termids[$key] = mb_convert_encoding($_termids[$key], 'HTML-ENTITIES', 'UTF-8');
							$_wp_term_id = mb_convert_encoding($_termids[$key], 'HTML-ENTITIES', 'UTF-8');
							$wordpress_blog_posts_old_tagids[] = $_wp_term_id;	
            			}	
					  	
					}
					
					
				}
				
				$post_magento_tag_ids = array();
				if($wordpress_blog_posts_old_tagids > 0)
				{
					$blog_tags = $this->tag->Create()->getCollection()->addFieldToSelect('tag_id');
        			$blog_tags->addFieldToFilter('term_id', array('in' => $wordpress_blog_posts_old_tagids));
					foreach($blog_tags as $key => $tag):
					if(isset($tag['tag_id']))
					{
					$post_magento_tag_ids[]  = $tag['tag_id'];
					}
					endforeach;
				}
				
				
					$wordpress_posts_featured_image = 'SELECT wm2.meta_value as featured_img
													FROM '.$_pref.'posts p1
													LEFT JOIN
														'.$_pref.'postmeta wm1
														ON (
															wm1.post_id = p1.id
															AND wm1.meta_value IS NOT NULL
															AND wm1.meta_key = "_thumbnail_id"
														)
													LEFT JOIN
														'.$_pref.'postmeta wm2
														ON (
															wm1.meta_value = wm2.post_id
															AND wm2.meta_key = "_wp_attached_file"
															AND wm2.meta_value IS NOT NULL
														)
													WHERE
														p1.ID="'.$wp_post_id.'"
														AND p1.post_type="post"
													ORDER BY
														p1.post_date DESC';
				if ($result = mysqli_query($blog_connection, $wordpress_posts_featured_image)) {
				 	while ($_featured_image = mysqli_fetch_assoc($result)) {
						
						foreach (['featured_img'] as $key) {
                			//$_termids[$key] = mb_convert_encoding($_termids[$key], 'HTML-ENTITIES', 'UTF-8');
							$_featured_image = mb_convert_encoding($_featured_image[$key], 'HTML-ENTITIES', 'UTF-8');
							$wordpress_blog_posts_info['featured_img'] = '/'.$_featured_image;	
							
							 
            			}	
					  	
					}
					
					
				}
				
				
				$post_name = $wordpress_blog_posts_info['post_title'];
				$posts = $this->post->Create()->getCollection();
        		$posts->addFieldToFilter('title', array('eq' => $post_name));
        		
				if ($posts->getFirstItem()->getPostId()):
					$this->_logger->info('Post :: '.$post_name .' Already Exists, So We Skipped it');
					$current_post_id = $posts->getFirstItem()->getPostId();
					
				else:
				
				
				if(isset($wordpress_blog_posts_info['featured_img']))
				{
				$featured_img = $wordpress_blog_posts_info['featured_img'];
				}else{
				$featured_img = null;
				}
				$customer_group_ids = array();
				 $customergroupcollection = $this->customergroup->getCollection();
        		foreach ($customergroupcollection as $value) {
            			$customer_group_ids[] = $value->getCustomerGroupId();
        		}
				$save_as_draft = true;
				if($wordpress_blog_posts_info['post_status']=='publish')
				{
				$save_as_draft = false;
				}else if($wordpress_blog_posts_info['post_status']=='pending')
				{
				$save_as_draft = true;
				}
				$post_date_gmt = $wordpress_blog_posts_info['post_date_gmt'];
				$post_date_gmt = $this->_timezoneInterface->date(new \DateTime($post_date_gmt))->format('Y-m-d H:i:s');
				
				$post_modified_gmt = $wordpress_blog_posts_info['post_modified_gmt'];
				$post_modified_gmt = $this->_timezoneInterface->date(new \DateTime($post_date_gmt))->format('Y-m-d H:i:s');
				
				
				$content = $wordpress_blog_posts_info['post_content'];
					$content = str_replace('<!--more-->', '<!-- pagebreak -->', $content);
				
				$content = preg_replace(
                '/src=[\'"]((http:\/\/|https:\/\/|\/\/)(.*)|(\s|"|\')|(\/[\d\w_\-\.]*))\/wp-content\/uploads(.*)((\.jpg|\.jpeg|\.gif|\.png|\.tiff|\.tif|\.svg)|(\s|"|\'))[\'"\s]/Ui',
                'src="$4{{media url="magebees_blog$6$8"}}$9"',
                $content
            );

            $content = $this->wordpressOutoutWrap($content);
				
				
				if($wordpress_blog_posts_info['post_name']!='')
				{
				$identifier = $wordpress_blog_posts_info['post_name'];
				}else{
				$identifier = $wordpress_blog_posts_info['post_title'];
				}
				$identifier = str_replace(' ', '-', strtolower($identifier)); // Replaces all spaces with hyphens.
				$identifier = preg_replace('/[^A-Za-z0-9\-]/', '', $identifier); // Removes special chars.
				
				
				$postData['title'] = $wordpress_blog_posts_info['post_title'];
				$postData['meta_title'] = '';
				$postData['meta_keywords'] = '';
				$postData['meta_description'] = '';
				$postData['identifier'] = $identifier;
				$postData['current_identifier'] = $identifier;
				$postData['content_heading'] = '';
				$postData['content'] = $content;
				$postData['creation_time'] = $post_date_gmt;
				$postData['update_time'] = $post_modified_gmt;
				$postData['publish_time'] = $post_date_gmt;
				$postData['save_as_draft'] = $save_as_draft;
				$postData['is_active'] = true;
				$postData['include_in_recent'] = true;
				$postData['position'] = 0;
				$postData['featured_img'] = $featured_img;
				$postData['author_id'] = $this->authSession->getUser()->getUserId();;
				$postData['media_gallery'] = null;
				$postData['secret'] = null;
				$postData['views_count'] = null;
				$postData['is_recent_posts_skip'] = false;
				$postData['short_content'] = 'false';
				$postData['customer_group'] = implode(',',(array)$customer_group_ids);
				$postData['store_id'] = implode(',',(array)$selectedStores);
				$postData['category_ids'] = implode(',',(array)$post_magento_cat_ids);;
				$postData['tag_ids'] = implode(',',(array)$post_magento_tag_ids);;
				$postData['products_id'] = null;
				$postData['related_post_ids'] = null;
				$postData['is_featured'] = false;
				$postData['is_imported'] = true;
				$postData['term_id'] = $wp_post_id;
				
				$post = $this->post->Create();
				$post->setData($postData);
				$post->save();
				$postData['post_id'] = $post->getPostId();
				
				$this->_eventManager->dispatch('magebees_blog_post_url', ['data' => $postData]);
				
				endif;
				
				
			}
			$response['success'] = true;	
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
