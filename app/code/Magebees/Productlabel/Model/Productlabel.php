<?php
/***************************************************************************
 Extension Name	: Product Label
 Extension URL	: https://www.magebees.com/product-label-extension-magento-2.html
 Copyright		: Copyright (c) 2016 MageBees, http://www.magebees.com
 Support Email	: support@magebees.com 
 ***************************************************************************/
 
namespace Magebees\Productlabel\Model;

class Productlabel extends \Magento\Framework\Model\AbstractModel
{
    protected $_info = [];
    protected $_date;
    protected $_catalogHelper;
	
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
	    \Magento\Catalog\Helper\Data $catalogHelper,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
		\Magebees\Productlabel\Model\RuleFactory $ruleFactory,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_date = $date;
        $this->ruleFactory = $ruleFactory;
        $this->_catalogHelper = $catalogHelper;
		$this->priceCurrency = $priceCurrency;
        $this->coreRegistry = $registry;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
     
     /**
      * Initialization
      *
      * @return void
      */
    protected function _construct()
    {
        $this->_init('Magebees\Productlabel\Model\ResourceModel\Productlabel');
    }
    
    public function getPosition()
    {
        return [
            [
                'value' => 'TL',
                'label' => __('Top-Left')
            ],
            [
                'value' => 'TC',
                'label' => __('Top-Center')
            ],
            [
                'value' => 'TR',
                'label' => __('Top-Right')
            ],
            [
                'value' => 'ML',
                'label' => __('Middle-Left')
            ],
            [
                'value' => 'MC',
                'label' => __('Middle-Center')
            ],
            [
                'value' => 'MR',
                'label' => __('Middle-Right')
            ],
            [
                'value' => 'BL',
                'label' => __('Bottom-Left')
            ],
            [
                'value' => 'BC',
                'label' => __('Bottom-Center')
            ],
            [
                'value' => 'BR',
                'label' => __('Bottom-Right')
            ]
        ];
    }
    
    public function validateData($object)
	{
		if (!empty($object['from_date']) && !empty($object['to_date'])) {
			$date = $this->_date;
			$fromTimestamp = $date->timestamp($object['from_date']);
			$toTimestamp = $date->timestamp($object['to_date']);
			return $fromTimestamp <= $toTimestamp;
		}
		return false;
	}

	public function init($mode, $product)
	{
		if ($mode) {
			$this->setMode($mode === 'category' ? 'cat' : 'prod');
		}
	}

	/**
	 * Get the label position
	 */
	public function getCssClass()
	{
		$positions = $this->getAvailablePositions(false);
		return $positions[$this->getValue('position')] ?? '';
	}

	public function getValue($key)
	{
		return $this->_getData($this->getMode() . '_' . $key);
	}

	public function getAvailablePositions($asText = true)
	{
		$positions = [];
		$prefixes = ['top', 'middle', 'bottom'];
		$suffixes = ['left', 'center', 'right'];

		foreach ($prefixes as $prefix) {
			foreach ($suffixes as $suffix) {
				$posKey = ucfirst($prefix[0]) . ucfirst($suffix[0]);
				$positions[$posKey] = $asText ? __(ucwords("$prefix $suffix")) : "$prefix-$suffix";
			}
		}
		return $positions;
	}

	/**
	 * Get the label text with variable substitution
	 */
	public function getText()
	{
		$product = $this->getProduct();
		$text = $this->getValue('text');

		preg_match_all('/{([a-zA-Z:\_0-9]+)}/', $text, $matches);
		$variables = $matches[1] ?? [];

		foreach ($variables as $var) {
			$value = $this->resolveVariable($var, $product);
			$text = str_replace('{' . $var . '}', $value, $text);
		}
		return $text;
	}

	protected function resolveVariable($var, $product)
	{
		$price = $this->_getPrices();
		switch ($var) {
			case 'PRICE':
				return $this->getFormatedPrice($price['price']);
			case 'SPECIAL_PRICE':
				return $this->getFormatedPrice($price['special_price']);
			case 'FINAL_PRICE':
				return $this->getFormatedPrice($this->_catalogHelper->getTaxPrice($product, $product->getFinalPrice(), false));
			case 'FINAL_PRICE_INCL_TAX':
				return $this->getFormatedPrice($this->_catalogHelper->getTaxPrice($product, $product->getFinalPrice(), true));
			case 'SAVE_AMOUNT':
				return $this->getFormatedPrice($price['price'] - $price['special_price']);
			case 'SAVE_PERCENT':
				return $price['price'] ? round(($price['price'] - $price['special_price']) * 100 / $price['price']) : 0;
			case 'BR':
				return '<br/>';
			case 'SKU':
				return $product->getSku();
			case 'NEW_FOR':
				$createdAt = strtotime($product->getCreatedAt());
				return max(1, floor((time() - $createdAt) / 86400));
			default:
				return '';
		}
	}

	public function isValid()
	{
		$product = $this->getProduct();

		if ($this->getData('cond_serialize')) {
			$ruleModel = $this->ruleFactory->create();
			$ruleModel->setConditionsSerialized($this->getData('cond_serialize'))->setProduct($product);

			if (!array_key_exists($product->getId(), $ruleModel->getMatchingProductIds())) {
				return false;
			}
		}

		if ($this->getIsNew() && $this->getIsNew() != ($this->isNew($product) ? 2 : 1)) {
			return false;
		}

		if ($this->getIsSale() && $this->getIsSale() != ($this->isSale($product) ? 2 : 1)) {
			return false;
		}

		if ($this->getDateEnabled()) {
			$now = $this->_date->gmtDate();
			if ($now < $this->getFromDate() || $now > $this->getToDate()) {
				return false;
			}
		}

		if ($this->getPriceEnabled() && !$this->isPriceValid($product)) {
			return false;
		}

		if ($this->getStockStatus() && $this->getStockStatus() != ($product->isSalable() ? 1 : 2)) {
			return false;
		}

		return true;
	}

	protected function isPriceValid($product)
	{
		$price = match ($this->getByPrice()) {
			'0' => $product->getPrice(),
			'1' => $product->getSpecialPrice(),
			'2' => $this->_catalogHelper->getTaxPrice($product, $product->getFinalPrice(), false),
			'3' => $this->_catalogHelper->getTaxPrice($product, $product->getFinalPrice(), true),
			default => null,
		};

		if ($product->getTypeId() === 'bundle') {
			$minPrice = $this->_catalogHelper->getTaxPrice($product, $product->getData('min_price'), true);
			$maxPrice = $this->_catalogHelper->getTaxPrice($product, $product->getData('max_price'), true);
			return $minPrice >= $this->getFromPrice() && $maxPrice <= $this->getToPrice();
		}

		return $price >= $this->getFromPrice() && $price <= $this->getToPrice();
	}

	public function isNew($product)
	{
		$fromDate = $product->getNewsFromDate();
		$toDate = $product->getNewsToDate();
		$now = time();

		if ($fromDate && $now >= strtotime($fromDate) && (!$toDate || $now <= strtotime($toDate))) {
			return true;
		}
		return false;
	}

	public function isSale($product)
	{
		$price = $this->_getPrices();
		return $price['price'] > 0 && $price['special_price'] > 0 && ($price['price'] - $price['special_price']) > 0.001;
	}

	protected function _getPrices()
	{
		if (!$this->_info) {
			$product = $this->getProduct();
			$regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
			$specialPrice = $this->getSpecialPrice($product);

			if ($product->getTypeId() === 'grouped') {
				foreach ($product->getTypeInstance(true)->getAssociatedProducts($product) as $child) {
					$regularPrice += $child->getPrice();
					$specialPrice += $child->getFinalPrice();
				}
			}

			$this->_info = [
				'price' => $regularPrice,
				'special_price' => $specialPrice,
			];
		}
		return $this->_info;
	}

	protected function getSpecialPrice($product)
	{
		$now = $this->_date->date('Y-m-d 00:00:00');

		if ($product->getSpecialFromDate() && $now >= $product->getSpecialFromDate()) {
			$specialPrice = $product->getPriceInfo()->getPrice('special_price')->getAmount()->getValue();

			if ($product->getSpecialToDate() && $now > $product->getSpecialToDate()) {
				$specialPrice = 0;
			}

			$finalPrice = $product->getPriceModel()->getFinalPrice(null, $product);
			return $finalPrice < $specialPrice ? $finalPrice : $specialPrice;
		}

		return $product->getPriceModel()->getFinalPrice(null, $product);
	}

	public function getFormatedPrice($amount)
	{
		return $this->priceCurrency->convertAndFormat($amount);
	}

}
