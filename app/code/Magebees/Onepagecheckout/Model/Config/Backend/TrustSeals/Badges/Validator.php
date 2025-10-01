<?php
namespace Magebees\Onepagecheckout\Model\Config\Backend\TrustSeals\Badges;
use Magebees\Onepagecheckout\Model\Config\Backend\TrustSeals\Badges;
use Magento\Framework\Validator\AbstractValidator;

class Validator extends AbstractValidator
{
    public function isValid($entity)
    {
        $this->_clearMessages();
        $value = $entity->getValue();
        $itemsCount = 0;
        foreach ($value as $badgeData) {
            if (isset($badgeData['script'])) {
                if (!isset($badgeData['script'])) {
                    $this->_addMessages(['Badge script is required.']);
                } else {
                    $itemsCount++;
                }
            }
        }
        if ($itemsCount > 5) {
            $this->_addMessages(['Maximum number of badge items 4 exceeded.']);
        }
        return empty($this->getMessages());
    }
}