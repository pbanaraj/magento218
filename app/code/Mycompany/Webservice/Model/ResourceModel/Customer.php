<?php
/**
 *  Customer Rest API Model to implement customer creation and update
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservicevar
 * @author Tavant Developer
 */

namespace Mycompany\Webservice\Model\ResourceModel;

use Magento\Customer\Model\Customer\NotificationStorage;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Validator\Exception as ValidatorException;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * 
 * @var Mycompany\Webservice\Model\ResourceModel\Customer
 * @author tavant
 *
 */

class Customer extends \Magento\Customer\Model\ResourceModel\Customer
{
    

    protected function _beforeSave(\Magento\Framework\DataObject $customer)
    {
             
        /** @var \Magento\Customer\Model\Customer $customer */
        if ($customer->getStoreId() === null) {
            $customer->setStoreId($this->storeManager->getStore()->getId());
        }
        $customer->getGroupId();
        
       
        
        if (!$customer->getEmail()) {
            throw new ValidatorException(__('Please enter a customer email.'));
        }
        
               
            // set confirmation key logic
            if ($customer->getForceConfirmed() || $customer->getPasswordHash() == '') {
                $customer->setConfirmation(null);
            } elseif (!$customer->getId() && $customer->isConfirmationRequired()) {
                $customer->setConfirmation($customer->getRandomConfirmationKey());
            }
            // remove customer confirmation key from database, if empty
            if (!$customer->getConfirmation()) {
                $customer->setConfirmation(null);
            }
            
            $this->_validate($customer);
            
            return $this;
    }
    
   
}
