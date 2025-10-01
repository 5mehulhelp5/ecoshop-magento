<?php
namespace Magebees\Responsivebannerslider\Model;
class Responsivebannerslider extends \Magento\Framework\Model\AbstractModel
{
	const NOROUTE_PAGE_ID = 'no-route';
	const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
	protected function _construct()
	{
		$this->_init('Magebees\Responsivebannerslider\Model\ResourceModel\Responsivebannerslider');
	}
	public function getPosition()
    {
        return [
            ['value' => 'content_top', 'label' => __('Content Top')],
            ['value' => 'content_bottom', 'label' => __('Content Bottom')],
           
        ];
    }
	public function getAnimationType()
    {
        return [
            ['value' => 'slide', 'label' => __('Slide')],
            ['value' => 'fade', 'label' => __('Fade')],
            ['value' => 'cube', 'label' => __('Cube')],
            ['value' => 'coverflow', 'label' => __('coverflow')],
            ['value' => 'flip', 'label' => __('flip')],
            ['value' => 'cards', 'label' => __('cards')],
            ['value' => 'creative', 'label' => __('creative')],
           
        ];
    }
	public function getAnimationDirection()
    {
        return [
            ['value' => 'horizontal', 'label' => __('Horizontal')],
            ['value' => 'vertical', 'label' => __('Vertical')],
           
        ];
    }
	
	public function getType()
    {
        return [
            ['value' => 'basic', 'label' => __('Basic slider')],
            ['value' => 'bas-caro', 'label' => __('Basic slider with carousel navigation')],
        ];
    }
	public function getNavigation()
    {
        return [
            ['value' => 'hover', 'label' => __('On hover')],
            ['value' => 'always', 'label' => __('Always')],
            ['value' => 'never', 'label' => __('Never')],
        ];
    }
	public function getNavigationstyle()
    {
        return [
            ['value' => 'style1', 'label' => __('Style 1')],
            ['value' => 'style2', 'label' => __('Style 2')],
            ['value' => 'style3', 'label' => __('Style 3')],
            ['value' => 'style4', 'label' => __('Style 4')],
            ['value' => 'style5', 'label' => __('Style 5')],
            ['value' => 'style6', 'label' => __('Style 6')],
            ['value' => 'style7', 'label' => __('Style 7')],
            
        ];
    }
	public function getNavigationarrow()
    {
        return [
            ['value' => 'inside', 'label' => __('Inside slider on both sides')],
            ['value' => 'outside', 'label' => __('Outside the slider on both sides')],
            ['value' => 'inside_left', 'label' => __('Inside slider grouped left')],
            ['value' => 'inside_right', 'label' => __('Inside slider grouped right')],
        ];
    }
	public function getPaginationstyle()
    {
        return [
            ['value' => 'circular', 'label' => __('Dynamic')],
            ['value' => 'numbers', 'label' => __('Numbers')],
            ['value' => 'progress_bar', 'label' => __('Progress bar')],
            ['value' => 'fraction_bar', 'label' => __('Fraction bar')],
        ];
    }
	public function getPaginationposition()
    {
        return [
            ['value' => 'inside_bottom', 'label' => __('Inside bottom slider')],
            ['value' => 'inside_bottom_left', 'label' => __('Inside bottom left')],
            ['value' => 'inside_bottom_right', 'label' => __('Inside bottom right')],
        ];
    }
	public function getAvailableStatuses()
    {
        return array(self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled'));
    }
}
