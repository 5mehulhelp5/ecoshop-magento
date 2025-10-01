<?php

namespace Magebees\Advertisementblock\Controller\Adminhtml\Manage;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{	
	protected $helper;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magebees\Advertisementblock\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($context);
    }
    public function execute()
    {
        
        $data = $this->getRequest()->getPost()->toarray();
        $id = $this->getRequest()->getParam('advertisement_id');
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
        $file_driver= $this->_objectManager->get('\Magento\Framework\Filesystem\Driver\File');
        $advertisementinfo_model = $this->_objectManager->create('Magebees\Advertisementblock\Model\Advertisementinfo');
        
        if ($data) {
            $filedata=$this->getRequest()->getFiles()->toArray();
            if ($id) {
                $advertisementinfo_model->load($id);
                /* start delete record from pattern images when pattern change*/
                $old_pattern=$advertisementinfo_model->getData('pattern');
                $new_pattern=$data['pattern'];
                if ($old_pattern!=$new_pattern) {
                    $advertisementimages_data = $this->_objectManager->create('Magebees\Advertisementblock\Model\Advertisementimages')->getCollection()->addFieldToFilter('advertisement_id', $id);
                        $advertisementimages_data->walk('delete');
                }
                /* end delete record from pattern images when pattern change*/
            }
            $advertisementinfo_model->setData($data);
            try {
                $pattern=$data['pattern'];
                $field_arr=$this->helper->getNumberOfBlock();
                $no_of_field=$field_arr[$pattern];
                for ($i=0; $i<$no_of_field; $i++) {
                    $img_data=[];
                    $index=$i+1;
                    
                    /*if((isset($advertisementimages_data[$i]['image_id'])) &&($this->getRequest()->getParam('advertisement_id')))
					{
						/* edit advertisement detail , check delete image here*/
                        /*$image_id=$advertisementimages_data[$i]['image_id'];
						$advertisementimages_model->load($image_id);
						$img_data['image_id']=$image_id;
						$del_index='img_del_'.$index;
						if(isset($data[$del_index]) && $data[$del_index]=='on'){
						$img=$advertisementimages_data[$i]['filename'];
						$val=$mediaDirectory->getAbsolutePath('magebees_advertisement');	
						$file_driver->deleteFile($val.$img);
						$img_data['filename']="";
						}
					}	*/
                    
                    
                    /* start image upload */
                    $img_field='filename_'.$index;
                    if (isset($filedata[$img_field]['name']) && $filedata[$img_field]['name'] != '') {
                        try {
                            $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => $img_field]);
                            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(true);
                        
                            $result = $uploader->save($mediaDirectory->getAbsolutePath('magebees_advertisement'));
                            unset($result['tmp_name']);							unset($result['path']);														$img_data['filename'] = $result['file'];
                            $img_size_arr=$this->helper->getPatternImageSize();
                            $dimension=$img_size_arr[$pattern][$i];
                            $dim_arr=explode('*', $dimension);
                            $width=$dim_arr[0];
                            $height=$dim_arr[1];
                            $this->helper->resizeImg($img_data['filename'], $width, $height);							$resizes_img_dir       = $this->helper->getResizedImgDir();							$img_path = $resizes_img_dir.$result['file'];							list($width, $height) = getimagesize($img_path);												  							$img_data['image_height'] = $height;							$img_data['image_width'] = $width;
                            $advertisementinfo_model->save();
                        } catch (\Exception $e) {
                            if ($this->getRequest()->getParam('advertisement_id')) {
                                $advertisementinfo_model->save();
                            }$this->messageManager->addException($e, __($e->getMessage()));
                            $this->messageManager->addException($e, __('Please Select Valid Image File'));
                            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('advertisement_id'), '_current' => true]);
                            return;
                        }
                        // $img_data['filename']=$filedata['filename'][$i]['name'];
                    } else {
                        $advertisementinfo_model->save();
                    }
                    $id=$advertisementinfo_model->getData('advertisement_id');
                    $advertisementimages_model = $this->_objectManager->create('Magebees\Advertisementblock\Model\Advertisementimages')->getCollection()->addFieldToFilter('advertisement_id', $id);
                    $advertisementimages_data=$advertisementimages_model->getData();
                    if ((isset($advertisementimages_data[$i]['image_id'])) && ($this->getRequest()->getParam('advertisement_id'))) {
                    /* edit advertisement detail , check delete image here*/
                        $image_id=$advertisementimages_data[$i]['image_id'];
                       // $advertisementimages_model->load($image_id);
                        $img_data['image_id']=$image_id;
                        $del_index='img_del_'.$index;
                        if (isset($data[$del_index]) && $data[$del_index]=='on') {
                            $img=$advertisementimages_data[$i]['filename'];
                            $val=$mediaDirectory->getAbsolutePath('magebees_advertisement');
                            $file_driver->deleteFile($val.$img);
                            $img_data['filename']="";
                        }
                    }
                    /* End  image upload */
                    
                    /*Start set data for save information in advertise image table*/
                    $img_data['advertisement_id']=$id;
                    $img_data['hover_heading_text']=$data['hover_heading_text_'.$index];
                    $img_data['show_hover_heading_text']=$data['show_hover_heading_text_'.$index];
                    $img_data['hover_short_text']=$data['hover_short_text_'.$index];
                    $img_data['show_hover_short_text']=$data['show_hover_short_text_'.$index];
                    $img_data['button_text']=$data['button_text_'.$index];
                    $img_data['show_button_text']=$data['show_button_text_'.$index];
                    $img_data['image_effect']=$data['image_effect_'.$index];
                    $img_data['external_url']=$data['external_url_'.$index];
                    $img_data['advertise_url']=$data['advertise_url_'.$index];
                    $img_data['text_position']=$data['text_position_'.$index];
                    
                    $img_data['display_text_mode']=$data['display_text_mode_'.$index];
                    $img_data['show_image']=$data['show_image_'.$index];
                    $img_data['bg_color']=$data['bg_color_'.$index];
                    $img_data['heading_text_color']=$data['heading_text_color_'.$index];
                    $img_data['heading_text_size']=$data['heading_text_size_'.$index];
                    $img_data['short_text_color']=$data['short_text_color_'.$index];
                    $img_data['short_text_size']=$data['short_text_size_'.$index];
                    $img_data['button_text_color']=$data['button_text_color_'.$index];
                    $img_data['button_bg_color']=$data['button_bg_color_'.$index];
                    $img_data['button_text_size']=$data['button_text_size_'.$index];
                    
                    
                    
                     $advertisementimages_model = $this->_objectManager->create('Magebees\Advertisementblock\Model\Advertisementimages');
                    $advertisementimages_model->setData($img_data);
                    $advertisementimages_model->save();
                    /*End set data for save information in advertise image table*/
                }
                $this->messageManager->addSuccess(__('Advertisement was successfully saved'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' =>$id, '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the slide.'));
            }
            
            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $id]);
            return;
        }
        $this->_redirect('*/*/');
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Advertisementblock::advertisementblock');
    }
}
