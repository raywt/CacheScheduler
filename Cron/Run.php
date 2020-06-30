<?php

namespace Raywt\CacheScheduler\Cron;

use Magento\Backend\App\Action\Context as Context;
use Magento\Backend\App\Action;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
use Psr\Log\LoggerInterface as Logger;
use Raywt\CacheScheduler\Helper\Config as ConfigHelper;

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
     * @var Raywt\CacheScheduler\Helper\Config
     */
    protected $_configHelper;

    /**
     * @param Logger $logger
     * @param Context $context
     * @param CacheTypeListInterface $cacheTypeList
     * @param CacheManager $cacheManager
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        Logger $logger,
        Context $context,
        CacheTypeListInterface $cacheTypeList,
        CacheManager $cacheManager,
        ConfigHelper $configHelper
    ) 
    {
        $this->_logger = $logger;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheManager = $cacheManager;
        $this->_configHelper = $configHelper;
    }

    /**
     * Not used. Might be used in a future version.
     *
     * @return void
     */
    public function cleanInvalidatedCaches()
    {
        $invalidcache = $this->_cacheTypeList->getInvalidated();
        foreach($invalidcache as $key => $value) {
          $this->_cacheTypeList->cleanType($key);
        }
    }

    /**
     * Check whether module is enabled
     *
     * @return boolean
     */
    public function getEnableStatus()
    {
        return $this->_configHelper->getEnabledSchedule();
    }

    /**
     * Get array of caches
     *
     * @return Array
     */
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

    /**
     * Clean all caches.
     *
     * @return void
     */
    public function cleanAllCaches()
    {
        //$this->cacheManager->flush($this->cacheManager->getAvailableTypes());

        $this->_cacheManager->clean($this->_cacheManager->getAvailableTypes());
    }

    /**
     * Clean all caches cron action
     *
     * @return void
     */
    public function execute()
    {
        if($this->getEnableStatus())
        {
            try {
                $this->cleanAllCaches();
                $this->_logger->debug('Scheduled Cache Clean has been run.');
            } catch(\Magento\Framework\Exception\LocalizedException $e) {
                $this->_logger->debug('Schedled Cache Clean failed: ' . $e->getMessage());
            } catch (\Exception $e) {
                $this->_logger->debug('Schedled Cache Clean failed.');
            }
            
        }
        return $this;
    }
}