<?php
namespace Magebees\Blog\Block\Adminhtml\Comment\Edit\Renderer;
/**
 * CustomFormField Customformfield field renderer
 */
class Overview extends \Magento\Framework\Data\Form\Element\AbstractElement {
    /**
     * Get the after element html.
     *
     * @return mixed
     */
	 protected $_comment;
	 protected $_post;
	 protected $request;
	 protected $url;
    public function __construct(\Magebees\Blog\Model\Post $post, \Magebees\Blog\Model\Comment $comment, \Magento\Framework\App\RequestInterface $request, \Magento\Framework\UrlInterface $url) {
        $this->_comment = $comment;
        $this->_post = $post;
        $this->request = $request;
        $this->url = $url;
    }
    public function getElementHtml() {
		$overview = null;
        $data = $this->request->getParams();
        if (isset($data['comment_id'])) {
            $comment_id = $data['comment_id'];
            $comment = $this->_comment->load($comment_id);
            $post_id = $comment->getPostId();
            $post = $this->_post->load($post_id);
            $post_url = $post_id;
            $post_url = $this->url->getUrl('blog/post/edit', ['post_id' => $post_id]);
            $post_title = '<a href="' . $post_url . '" target="_blank"> # ' . $post->getPostId() . " " . $post->getTitle() . '</a>';
            $author_info = '<a href="mailto:' . $comment->getAuthorEmail() . '" target="_top">' . $comment->getAuthorNickname() . ' - ' . $comment->getAuthorEmail() . '(' . $comment->getAuthorType() . ')</a>';
        
        $overview = '<div id="customdiv">';
        $overview.= '<p><strong> Post: </strong>' . $post_title . '</p>';
        $overview.= '<p><strong>Author : </strong>' . $author_info . '</p>';
        if ($comment->getParentId()):
            $parent_comment_id = $comment->getParentId();
            $parent_comment = $this->_comment->load($parent_comment_id);
            $parent_comment_url = $this->url->getUrl('blog/comment/edit', ['comment_id' => $parent_comment_id]);
            $parent_comment_title = '<a href="' . $parent_comment_url . '" target="_blank"> # ' . $parent_comment_id . " " . $parent_comment->getText() . '</a>';
            $overview.= '<p><strong> Parent Comment: </strong>' . $parent_comment_title . '</p>';
        endif;
        $overview.= '</div>';
        }
		if (isset($data['parent_comment_id'])) {
            $comment_id = $data['parent_comment_id'];
            $comment = $this->_comment->load($comment_id);
            $post_id = $comment->getPostId();
			$post = $this->_post->load($post_id);
            $post_url = $this->url->getUrl('blog/post/edit', ['post_id' => $post_id]);
            $post_title = '<a href="' . $post_url . '" target="_blank"> # ' . $post->getPostId() . " " . $post->getTitle() . '</a>';
            $overview = '<div id="customdiv">';
        	$overview.= '<p><strong>Post: </strong>' . $post_title . '</p>';
            $parent_comment = $this->_comment->load($comment_id);
            $parent_comment_url = $this->url->getUrl('blog/comment/edit', ['comment_id' => $comment_id]);
            $parent_comment_title = '<a href="' . $parent_comment_url . '" target="_blank"> # ' . $comment_id . " " . $parent_comment->getText() . '</a>';
            $overview.= '<p><strong>Parent Comment: </strong>' . $parent_comment_title . '</p>';
        
        $overview.= '</div>';
        }
		$overview.= '<style>#customdiv { padding-left:52%; }</style>';
		return $overview;
    }
}


