<?php
/***************************************************************************
 Extension Name  : Magento2 Responsive Banner Slider with Lazy Load Extension
 Extension URL   : http://www.magebees.com/magento2-responsive-banner-slider-with-lazy-load-extension.html
 Copyright    : Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email   : support@magebees.com 
 ***************************************************************************/
 ?>
<?php
namespace Magebees\Responsivebannerslider\Block\Adminhtml\Slidergroup\Edit;
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
   
    protected $_customerAccountService;

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            array(
                'data' => array(
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                )
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
