<?php
namespace Magebees\Responsivebannerslider\Model;
class Slide extends \Magento\Framework\Model\AbstractModel
{
	const NOROUTE_PAGE_ID = 'no-route';
	const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
	protected $_date;
	
	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
		$this->_date = $date;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
	
	protected function _construct()
	{
		$this->_init('Magebees\Responsivebannerslider\Model\ResourceModel\Slide');
	}
	
	public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRoutePage();
        }
        return parent::load($id, $field);
    }

    public function noRoutePage()
    {
        return $this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName());
    }
	
	public function getAvailableVideo()
    {
        return [
            ['value' => 'image', 'label' => __('Image')],
            ['value' => 'youtube', 'label' => __('Youtube Video')],
            ['value' => 'vimeo', 'label' => __('Vimeo Video')]
        ];
    }
	public function getAvailableStatuses()
    {
        return array(self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled'));
    }

    public function getContentPosition()
    {
        return [
            ['value' => 'left', 'label' => __('Left')],
            ['value' => 'leftTop', 'label' => __('Left Top')],
            ['value' => 'leftBottom', 'label' => __('Left Bottom')],
            ['value' => 'right', 'label' => __('Right')],
            ['value' => 'rightTop', 'label' => __('Right Top')],
            ['value' => 'rightBottom', 'label' => __('Right Bottom')],
            ['value' => 'center', 'label' => __('Center')],
            ['value' => 'centerTop', 'label' => __('Center Top')],
            ['value' => 'centerBottom', 'label' => __('Center Bottom')],
        ];
    }
	public function getUrlTarget()
    {
        return [
            ['value' => 'same_window', 'label' => __('Same Window / Tab')],
            ['value' => 'new_window', 'label' => __('New Window / Tab')],
        ];
    }
	
	public function validateData($object)  {
		$fromDate = $object['from_date'];
        $toDate = $object['to_date'];
		if($fromDate != "" && $toDate != "") {
			$date = $this->_date;
			$value = $date->timestamp($fromDate);
			$maxValue = $date->timestamp($toDate);
				if ($value > $maxValue) {
					return false;
				}else{
					return true;
				}
		}
		return false;	
	}
}
