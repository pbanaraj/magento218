<?php
/**
 *  Accounts Controller to implement custom login
 * @category Mycompany 
 * @package Mycompany_Webservice
 * @module Webservice
 * @author Tavant Developer
 */

namespace Mycompany\Webservice\Controller;

use Magento\Framework\ObjectManagerInterface;

/**
 * 
 * @var Mycompany\Webservice\Controller\Accounts
 * @author tavant
 *
 */

abstract class Accounts extends \Magento\Customer\Controller\AbstractAccount
{

    

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession
        
        
    ) {
        $this->_customerSession = $customerSession;
      
        parent::__construct($context);
        
    }


    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession ()
    {
        return $this->_customerSession;
    }
}
