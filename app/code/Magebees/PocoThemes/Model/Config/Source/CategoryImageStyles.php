<?php
namespace Magebees\PocoThemes\Model\Config\Source;
use Magento\Framework\Option\ArrayInterface;

class CategoryImageStyles implements ArrayInterface
{
   public function toOptionArray()
   {
       $options = [
           [
               'value' => 'Magebees_CategoryImage::style1.phtml',
               'label' => __('PocoThemes Style 1')
           ],
           [
               'value' => 'Magebees_CategoryImage::style2.phtml',
               'label' => __('PocoThemes Style 1')
           ]
       ];

       return $options;
   }
}