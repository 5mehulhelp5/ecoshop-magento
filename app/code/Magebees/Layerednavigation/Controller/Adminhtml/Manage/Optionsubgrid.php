<?php

namespace  Magebees\Layerednavigation\Controller\Adminhtml\Manage;

class Optionsubgrid extends Optiongrid
{
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Layerednavigation::layerednavigation');
    }
}
