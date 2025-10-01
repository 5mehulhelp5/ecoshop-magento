<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Model;

use Magebees\CacheWarmer\Api\Data\FlushesLogInterface;
use Magento\Framework\Model\AbstractModel;

class FlushesLog extends AbstractModel implements FlushesLogInterface
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\FlushesLog::class);
        $this->setIdFieldName(FlushesLogInterface::LOG_ID);
    }

    public function getLogId(): int
    {
        return (int)$this->getData(FlushesLogInterface::LOG_ID);
    }

    public function setLogId(int $id): FlushesLogInterface
    {
        return $this->setData(FlushesLogInterface::LOG_ID, $id);
    }

    public function getSource(): string
    {
        return $this->getData(FlushesLogInterface::SOURCE);
    }

    public function setSource(string $source): FlushesLogInterface
    {
        return $this->setData(FlushesLogInterface::SOURCE, $source);
    }

    public function getDetails(): string
    {
        return $this->getData(FlushesLogInterface::DETAILS);
    }

    public function setDetails(string $details): FlushesLogInterface
    {
        return $this->setData(FlushesLogInterface::DETAILS, $details);
    }

    public function getTags(): string
    {
        return $this->getData(FlushesLogInterface::TAGS);
    }

    public function setTags(string $tags): FlushesLogInterface
    {
        return $this->setData(FlushesLogInterface::TAGS, $tags);
    }

    public function getSubject(): string
    {
        return $this->getData(FlushesLogInterface::SUBJECT);
    }

    public function setSubject(string $subject): FlushesLogInterface
    {
        return $this->setData(FlushesLogInterface::SUBJECT, $subject);
    }

    public function getDate(): string
    {
        return $this->getData(FlushesLogInterface::DATE);
    }

    public function setDate(string $date): FlushesLogInterface
    {
        return $this->setData(FlushesLogInterface::DATE, $date);
    }

    public function getBacktrace(): string
    {
        return $this->getData(FlushesLogInterface::BACKTRACE);
    }

    public function setBacktrace(string $backtrace): FlushesLogInterface
    {
        return $this->setData(FlushesLogInterface::BACKTRACE, $backtrace);
    }
}
