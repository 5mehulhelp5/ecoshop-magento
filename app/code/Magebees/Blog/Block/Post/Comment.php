<?php
namespace Magebees\Blog\Block\Post;
class Comment extends \Magebees\Blog\Block\Post\View {
    public function getCaptchaSettigns() {
        return $this->configuration->getConfig('blog/post_view/comment/recaptcha');
    }
    public function getCurrentUrl() {
        return $this->_urlInterface->getCurrentUrl();
    }
    public function getDefaultStatus() {
        return $this->configuration->getConfig('blog/post_view/comment/comment_status');
    }
    public function getCommentscount($post_id) {
        $no_of_comments = $this->configuration->getConfig('blog/post_view/comment/number_of_comments');
        $comment = $this->_blogcomment->getCollection();
        $comment->addFieldToFilter('post_id', array('eq' => $post_id));
        $comment->addFieldToFilter('status', array('eq' => 1));
        $comment->addFieldToFilter('parent_id', array('eq' => 0));
        $comment->getSelect()->limit($no_of_comments);
        return count($comment->getData());
    }
    public function getPrivacyPolicyUrl() {
        $enable_privacy_policy = $this->configuration->getConfig('blog/post_view/comment/privacy_policy');
        if ($enable_privacy_policy):
            $policyPage = $this->_page->load('privacy-policy-cookie-restriction-mode');
            if ($policyPage->getIsActive()):
                return $this->_urlInterface->getUrl('privacy-policy-cookie-restriction-mode');
            endif;
        endif;
        return null;
    }
    public function getPrivacyPolicyTitle() {
        $enable_privacy_policy = $this->configuration->getConfig('blog/post_view/comment/privacy_policy');
        if ($enable_privacy_policy):
            $policyPage = $this->_page->load('privacy-policy-cookie-restriction-mode');
            if ($policyPage->getIsActive()):
                return $policyPage->getTitle();
            endif;
        endif;
        return null;
    }
    public function IsAllowComment() {
        $allow_for_guest = $this->configuration->getConfig('blog/post_view/comment/allow_guest');
        if ($allow_for_guest) {
            return true;
        } else {
            $cutomerId = $this->_customerSession->getCustomer()->getId();
            if ($cutomerId) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
    public function getCustomerId() {
        $customerData = $this->_customerSession->getCustomer();
        return $this->_customerSession->getCustomer()->getId();
    }
    public function getCustomerEmailId() {
        return $this->_customerSession->getCustomer()->getEmail();
    }
    public function getCustomerName() {
        return ($this->_customerSession->getCustomer()->getFirstname() . " " . $this->_customerSession->getCustomer()->getLastname());
    }
	public function getCommentCreationDate($comment)
	{
		$post_comment_date_format = $this->configuration->getConfig('blog/post_view/comment/date_format');
			if(!$post_comment_date_format):
				$post_comment_date_format = 'F d, Y H:i:s';
			endif;
		if($comment->getCreationTime()): 
			$publish_time = $comment->getCreationTime();
		//return $this->_timezoneInterface->date($publish_time)->format($post_comment_date_format);
		$publish_date=date_create($publish_time);
		
		return $_post_publish_date=date_format($publish_date,$post_comment_date_format);
		endif;
		return null;	
	
	}
	
    public function getCommentlist($post_id) {
        $no_of_comments = $this->configuration->getConfig('blog/post_view/comment/number_of_comments');
        $comment_html = '';
        $comments = $this->_blogcomment->getCollection();
        $comments->addFieldToFilter('post_id', array('eq' => $post_id));
        $comments->addFieldToFilter('status', array('eq' => 1));
        $comments->addFieldToFilter('parent_id', array('eq' => 0));
        $comments->getSelect()->limit($no_of_comments);
        foreach ($comments as $comment):
            $comment_id = $comment->getCommentId();
            $comment_html.= '<div class="c-comment c-parent c-comment-parent-' . $comment->getParentId() . '">';
            $comment_html.= '<div class="c-post c-post-1" id="c-post-' . $comment_id . '">';
            $comment_html.= '<div class="p-name" id="c-post-' . $comment_id . '">' . $comment->getAuthorNickname() . '</div>';
            $comment_html.= '<div class="p-text">' . $comment->getText() . '</div>';
            $comment_html.= '<div class="p-actions">';
            if ($this->IsAllowComment()):
                $comment_html.= '<a href="#" class="reply-me" comment-id=' . $comment_id . ' title="Reply">Reply</a>';
            endif;
            $comment_html.= '<span class="publish-date"> ' . $this->getCommentCreationDate($comment) . '</span>';
            $comment_html.= '</div>';
            if ($this->getChildCommentCount($comment_id, $post_id) > 0) {
                $child_comment_html = $this->getChildCommentlist($comment_id, $post_id);
                $comment_html.= $child_comment_html;
            }
            $comment_html.= '</div>';
            $comment_html.= '</div>';
        endforeach;
        return $comment_html;
    }
    public function getChildCommentCount($comment_id, $post_id) {
        $no_of_reply = $this->configuration->getConfig('blog/post_view/comment/number_of_replied');
        if ($no_of_reply > 0) {
            $comments = $this->_blogcomment->getCollection();
            $comments->addFieldToFilter('post_id', array('eq' => $post_id));
            $comments->addFieldToFilter('status', array('eq' => 1));
            $comments->addFieldToFilter('parent_id', array('eq' => $comment_id));
            $comments->getSelect()->limit($no_of_reply);
            return count($comments->getData());
        } else {
            return 0;
        }
    }
    public function getChildCommentlist($comment_id, $post_id) {
        $no_of_reply = $this->configuration->getConfig('blog/post_view/comment/number_of_replied');
        if ($no_of_reply > 0) {
            $comment_html = '';
            $comments = $this->_blogcomment->getCollection();
            $comments->addFieldToFilter('post_id', array('eq' => $post_id));
            $comments->addFieldToFilter('status', array('eq' => 1));
            $comments->addFieldToFilter('parent_id', array('eq' => $comment_id));
            $comments->getSelect()->limit($no_of_reply);
            foreach ($comments as $comment):
                $comment_id = $comment->getCommentId();
                $comment_html.= '<div class="c-comment c-comment-parent c-comment-parent-' . $comment->getParentId() . '">';
                $comment_html.= '<div class="c-post c-post-1" id="c-post-' . $comment->getCommentId() . '">';
                $comment_html.= '<div class="p-name" id="c-post-' . $comment->getCommentId() . '">' . $comment->getAuthorNickname() . '</div>';
                $comment_html.= '<div class="p-text">' . $comment->getText() . '</div>';
                $comment_html.= '<div class="p-actions">';
                if ($this->IsAllowComment()):
                    $comment_html.= '<a href="#" class="reply-me" comment-id=' . $comment->getCommentId() . ' title="Reply">Reply</a>';
                endif;
                $comment_html.= '<span class="publish-date"> ' . $this->getCommentCreationDate($comment) . '</span>';
                $comment_html.= '</div>';
                if ($this->getChildCommentCount($comment_id, $post_id) > 0) {
                    $child_comment_html = $this->getChildCommentlist($comment_id, $post_id);
                    $comment_html.= $child_comment_html;
                }
                $comment_html.= '</div>';
                $comment_html.= '</div>';
            endforeach;
        } else {
            return 0;
        }
        return $comment_html;
    }
}

