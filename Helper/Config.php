<?php
namespace Raywt\CacheScheduler\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const ENABLE_SCHEDULER = 'cache_scheduler/cron/enable_schedule';

    public function getEnabledSchedule()
    {
        return $this->scopeConfig->getValue(static::ENABLE_SCHEDULER);
    }
}