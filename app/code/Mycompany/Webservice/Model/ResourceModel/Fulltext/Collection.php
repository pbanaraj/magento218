<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mycompany\Webservice\Model\ResourceModel\Fulltext;

use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Magento\Framework\Search\Response\QueryResponse;
use Magento\Framework\Search\Request\EmptyRequestDataException;
use Magento\Framework\Search\Request\NonExistingRequestNameException;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;

/**
 * Fulltext Collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
{
    
    /*
     
     public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Search\Model\QueryFactory $catalogSearchData,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        $searchRequestName = 'catalog_view_container',
        SearchResultFactory $searchResultFactory = null
        ) {
            
            parent::__construct(
                $entityFactory,
                $logger,
                $fetchStrategy,
                $eventManager,
                $eavConfig,
                $resource,
                $eavEntityFactory,
                $resourceHelper,
                $universalFactory,
                $storeManager,
                $moduleManager,
                $catalogProductFlatState,
                $scopeConfig,
                $productOptionFactory,
                $catalogUrl,
                $localeDate,
                $customerSession,
                $dateTime,
                $groupManagement,
                $catalogSearchData,
                $requestBuilder,
                $searchEngine,
                $temporaryStorageFactory,
                $connection,
                $searchRequestName,
                $searchResultFactory
                );
           
            
            $this->requestBuilder = $requestBuilder;
            $this->searchEngine = $searchEngine;
            $this->temporaryStorageFactory = $temporaryStorageFactory;
            $this->searchRequestName = $searchRequestName;
            
    }
    
    
    
    
    public function setOrder($attribute, $dir = Select::SQL_DESC)
    {
        
        echo 'Helo';
        die();
        $this->getSelect()->getAdapter()->orderRand($this->getSelect());
        return $this;
    }
    
    */
    
    public function aroundSetOrder($subject, $proceed,$attribute){
        //if($attribute=="random"){
            $subject->getSelect()->order('rand()');
      //  }
        return $proceed;
    }
    
    
}

