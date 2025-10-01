<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Controller\Adminhtml\Productlabel;

class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
