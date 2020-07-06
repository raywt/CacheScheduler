<?php

namespace JoE\CacheScheduler\Cron;

use Magento\Backend\App\Action\Context as Context;
use Magento\Backend\App\Action;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\Event\ManagerInterface as ManagerInterface;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
use Psr\Log\LoggerInterface as Logger;
use JoE\CacheScheduler\Helper\Config as ConfigHelper;

class Run 
{
    /**
     * @var Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var Magento\Framework\App\Cache\Manager
     */
    protected $_cacheManager;

    /**
     * @var JoE\CacheScheduler\Helper\Config
     */
    protected $_configHelper;

    /**
     * @var Magento\Framework\Event\ManagerInterface
     */
    protected $_managerInterface;

    /**
     * @param Logger $logger
     * @param Context $context
     * @param CacheTypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     * @param CacheManager $cacheManager
     * @param ConfigHelper $configHelper
     * @param ManagerInterface $managerInterface
     */
    public function __construct(
        Logger $logger,
        Context $context,
        CacheTypeListInterface $cacheTypeList,
        CacheManager $cacheManager,
        ConfigHelper $configHelper,
        ManagerInterface $managerInterface
    ) 
    {
        $this->_logger = $logger;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheManager = $cacheManager;
        $this->_configHelper = $configHelper;
        $this->_managerInterface = $managerInterface;
    }

    public function cleanInvalidatedCaches()
    {
        $invalidcache = $this->_cacheTypeList->getInvalidated();
        foreach($invalidcache as $key => $value) {
          $this->_cacheTypeList->cleanType($key);
        }
    }

    public function getEnableStatus()
    {
        return $this->_configHelper->getEnabledSchedule();
    }

    public function getCaches()
    {
        $caches = $this->_cacheManager->getAvailableTypes();
        $cachelist = '';
        foreach($caches as $cache)
        {
            $cachelist .= $cache;
        }
        return $cachelist;
    }

    public function cleanAllCaches()
    {
        $this->_managerInterface->dispatch('adminhtml_cache_flush_all');
        $this->_cacheManager->flush($this->_cacheManager->getAvailableTypes());
    }

    public function execute()
    {
        if($this->getEnableStatus())
        {
            $this->_logger->debug('CacheScheduler about to flush caches');
            $this->cleanAllCaches();
            $this->_logger->debug('CacheScheduler Cache cleaned on schedule');
        }
        return $this;
    }
}
