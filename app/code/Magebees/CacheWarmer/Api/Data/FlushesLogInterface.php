<?php

declare(strict_types=1);

/**
 * @author Magebees Team
 * @copyright Copyright (c) Magebees (https://www.magebees.com)
 * @package Full Page Cache Warmer for Magento 2
 */

namespace Magebees\CacheWarmer\Api\Data;

interface FlushesLogInterface
{
    public const LOG_ID = 'log_id';
    public const SOURCE = 'source';
    public const DETAILS = 'details';
    public const TAGS = 'tags';
    public const SUBJECT = 'subject';
    public const DATE = 'date';
    public const BACKTRACE = 'backtrace';

    public function getLogId(): int;

    public function setLogId(int $id): self;

    public function getSource(): string;

    public function setSource(string $source): self;

    public function getDetails(): string;

    public function setDetails(string $details): self;

    public function getTags(): string;

    public function setTags(string $tags): self;

    public function getSubject(): string;

    public function setSubject(string $subject): self;

    public function getDate(): string;

    public function setDate(string $date): self;

    public function getBacktrace(): string;

    public function setBacktrace(string $backtrace): self;
}
