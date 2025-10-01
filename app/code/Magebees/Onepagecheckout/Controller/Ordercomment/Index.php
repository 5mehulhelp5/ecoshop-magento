<?php
namespace Magebees\Onepagecheckout\Controller\Ordercomment;
class Index extends  \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $result = $this->_objectManager->create('\Magento\Framework\Controller\Result\JsonFactory')->create();
        $success = 0;
        $fileuplaodstatus = 0;
        $ordercommentsstatus = 0;
        $fileuplaodvalue = "";
        $checkoutSession = $this->_objectManager->create('\Magento\Checkout\Model\Session');
        $checkoutSession->setOrderCommentsdata(1);
        $checkoutSession->setFileuploadvaluestatus(0);
		$OrdercommentsArray = $this->getRequest()->getPost('order_comments');
		$image = $this->getRequest()->getFiles('order_for');
		
		$Ordercomments = "";
        if (!empty($OrdercommentsArray)) {
			foreach($OrdercommentsArray as $Ocomments)
			{
				if($Ocomments == ""){
					$Ordercomments .= '|';
				}else{
					$Ordercomments .= nl2br($Ocomments).'|';
				}
			}
			$Ordercomments = substr($Ordercomments, 0, -1);
            $checkoutSession->setOrderCommentstext($Ordercomments);
            $ordercommentsstatus = 1;
            $checkoutSession->setOrdercommentsstatus($ordercommentsstatus);
            $success = 1;
        } else {
            $Ordercomments = "";
            $checkoutSession->setOrderCommentstext($checkoutSession->getOrderCommentstext());
            $ordercommentsstatus = 1;
            $checkoutSession->setOrdercommentsstatus($ordercommentsstatus);
        }

		$fileName = "";
		$newfileName = "";
		if(!empty($image)){				
			$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);	
			$uploaderObj = $this->_objectManager->create('\Magento\MediaStorage\Model\File\UploaderFactory');
			foreach($image as $key  => $img){
				$fileName .= str_replace(' ', '_', $img['name']).',';
				$newfileName .= $img['name'].',';
				try {
					$OrdercommentFiletype = $this->_objectManager->create('Magebees\Onepagecheckout\Helper\Data')->getOrdercommentFiletype();
					$uploader = $uploaderObj->create(['fileId' => $img]);
					if ($OrdercommentFiletype) {
					   $OrdercommentFiletypeArray = explode(',',$OrdercommentFiletype);
					   $uploader->setAllowedExtensions($OrdercommentFiletypeArray);
					} else {
					   $uploader->setAllowedExtensions(['jpg','jpeg','gif','png','txt','exe','psd','csv','doc']);
					}
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(false);
					$uploader->save($mediaDirectory->getAbsolutePath('Magebees\Onepagecheckout'));
				} catch (\Exception $e) {
					
				}
			}
			try{
				$success = 1;
				$fileuplaodstatus = 1;
				$fileuplaodvalue = substr($fileName, 0, -1);
				$checkoutSession->setFileuploadstatus($fileuplaodstatus);
				$checkoutSession->setFileuploadvalue($fileuplaodvalue);
				$checkoutSession->setFileuploadvaluestatus(0);
			}catch (\Exception $e) {
				if ($e->getCode() == 0) {
					$checkoutSession->setFileuploadstatus('');
					$checkoutSession->setFileuploadvalue('');
					$checkoutSession->setFileuploadvaluestatus(1);
				}
			}
			$checkoutSession->setOrderForFile($fileName);	
        } else {
            $checkoutSession->setOrderForFile('');
            $fileuplaodstatus = 1;
            $checkoutSession->setFileuploadstatus($fileuplaodstatus);
            $checkoutSession->setFileuploadvalue($checkoutSession->getFileuploadvalue());
            $checkoutSession->setFileuploadvaluestatus(0);
        }
        if($success == 1){			
            if(isset($newfileName) && !empty($newfileName)){
				$imagename = rtrim($newfileName,",");
            }else{
                $imagename = $checkoutSession->getFileuploadvalue();
            }
            $result->setData(['success'=>'true','comment' => $checkoutSession->getOrderCommentstext(),'ordercommentstatus' =>$checkoutSession->getOrdercommentsstatus(),'filename' => $imagename,'fileuploadstauts' =>$checkoutSession->getFileuploadstatus()]);
        }else{
            $result->setData(['success'=>'false']);
        }
        return $result;
    }
}