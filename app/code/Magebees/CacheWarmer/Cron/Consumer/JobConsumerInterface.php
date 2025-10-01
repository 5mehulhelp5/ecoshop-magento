<?php
namespace Magebees\CacheWarmer\Cron\Consumer;

interface JobConsumerInterface
{
    public function consume();
}
