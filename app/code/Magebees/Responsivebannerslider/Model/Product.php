<?php
namespace Magebees\Responsivebannerslider\Model;
class Product extends \Magento\Framework\Model\AbstractModel
{
	const NOROUTE_PAGE_ID = 'no-route';
		
	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
	
	protected function _construct()
	{
		$this->_init('Magebees\Responsivebannerslider\Model\ResourceModel\Product');
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

}
