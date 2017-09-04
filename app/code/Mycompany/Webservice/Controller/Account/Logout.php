<?php

/**
 *  Logout Controller to implement custom logout
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservicevar
 * @author Tavant Developer
 */

namespace Mycompany\Webservice\Controller\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;

/**
 * 
 * @var Mycompany\Webservice\Controller\Account\Logout
 * @author tavant
 *
 */

class Logout extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var Session
     */
    protected $session;
    
    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;
    
    /**
     * @var PhpCookieManager
     */
    private $cookieMetadataManager;
    
    /**
     * @param Context $context
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Mycompany\Webservice\Model\NaylorloginsFactory $NaylorloginsFactory
        ) {
            $this->session = $customerSession;
            $this->NaylorloginsFactory = $NaylorloginsFactory;
            parent::__construct($context,$customerSession);
    }
    
    /**
     * Retrieve cookie manager
     *
     * @deprecated
     * @return PhpCookieManager
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(PhpCookieManager::class);
        }
        return $this->cookieMetadataManager;
    }
    
    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated
     * @return CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        }
        return $this->cookieMetadataFactory;
    }
    
    /**
     * Customer logout action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
     
        $lastCustomerId = $this->session->getId();
        $nlogins = $this->NaylorloginsFactory->create();
        $nlogsin = $nlogins->load($lastCustomerId,'customer_id');
        $nlogsin->setAccessToken('');
        $nlogsin->setCreatedAt(time());
        $nlogsin->save();
        
        $this->session->logout()->setBeforeAuthUrl($this->_redirect->getRefererUrl())
        ->setLastCustomerId($lastCustomerId);
        if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
            $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
            $metadata->setPath('/');
            $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
        }
        
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/logoutSuccess');
        return $resultRedirect;
    }
}
