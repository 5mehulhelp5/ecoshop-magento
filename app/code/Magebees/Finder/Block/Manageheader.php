<?php
namespace Magebees\Finder\Block;

class Manageheader extends \Magento\Framework\View\Element\Template
{

   
    public function manageHeaderContent()
    {
        $this->pageConfig->addPageAsset('Magebees_Finder::css/mbfinder.css');
    }
}
