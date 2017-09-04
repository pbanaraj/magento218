<?php

/**
 *  Customers Rest API Model to implement customer creation and update
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservicevar
 * @author Tavant Developer
 */


namespace Mycompany\Webservice\Model;

use Mycompany\Webservice\Api\CustomersInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;

/**
 * @var Mycompany\Webservice\Model\Customers
 * 
 * @author tavant
 *
 *
 */
class Customers implements CustomersInterface
{
    
    protected $customersFactory;
    protected $StoreManager;
    protected $DirectoryList;
    protected $File;
    protected $addressFactory;
    protected $collection;
    
    
    
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customersFactory,
        \Magento\Customer\Model\Customer $customers,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $custCollectionFactory,
        StoreManagerInterface $StoreManager,
        DirectoryList $DirectoryList,
        File $File,       
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepoInterface,
        \Magento\Framework\App\Request\Http $request,
        \Mycompany\Webservice\Model\NaylorloginsFactory $NaylorloginsFactory,
        \Magento\Integration\Model\Oauth\TokenFactory $tokenFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
        )
    {
        $this->customerFactory = $customersFactory;
        $this->storeManager =  $StoreManager;    
        $this->directoryList =  $DirectoryList;
        $this->file =  $File;
        $this->addressFactory =  $addressFactory;
        $this->_customerRepoInterface = $customerRepoInterface;
        $this->collection = $customers->getCollection();
        $this->NaylorloginsFactory = $NaylorloginsFactory;
        $this->request = $request;
        $this->tokenFactory = $tokenFactory;
        $this->custCollectionFactory = $custCollectionFactory;
        $this->addressRepository = $addressRepository;
        
    }
           
    /**
     * Sum an array of numbers.
     *
     * @api
     * @param mixed $customersData of data to add as as customers attribute.
     * @return int The sum of the numbers.
     */
    public function addAttributes($customersData) {
        
        $bearer_access_token = substr($this->request->getHeader('Authorization'),7);
        
        $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
        
    
        
        $count_rec = count($customersData);
        
       if($count_rec == 1){       
       
           $exist_email= $this->custCollectionFactory->create()   
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('email', $customersData[0]['customer']['email'])
            ->addAttributeToFilter('source_sys_uid',$customersData[0]['customer']['source_sys_uid'])
            ->addAttributeToFilter('association_id',$customersData[0]['customer']['association_id']);
            $existdata=$exist_email->getData();
           
           
            
            if(isset($existdata) && count($existdata) >= 1){
                $tokens = $this->tokenFactory->create();
                $tokens = $tokens->load($bearer_access_token,'token');
             
                 $findtoken = $tokens->getToken();
                
                if($findtoken){
                    $naylorobj = $this->NaylorloginsFactory->create();
                    $naylorobj->setAccessToken($findtoken);
                    $naylorobj->setCustomerId($existdata[0]['entity_id']);
                    $naylorobj->setAssociationId($existdata[0]['association_id']);
                    $naylorobj->setNaylorEmail($existdata[0]['email']);
                    $naylorobj->setCreatedAt(time());
                    $naylorobj->save();
                    
                    // save customer data
                    $cust = $this->customerFactory->create();
                    $cust->load($existdata[0]['entity_id'],'entity_id');
                    $cust->setFirstname($customersData[0]['customer']['firstname']);
                    $cust->setLastname($customersData[0]['customer']['lastname']);
                    $cust->setData("product_type",$customersData[0]['customer']['product_type']);
                    $cust->setData("association_name",$customersData[0]['customer']['association_name']);
                    $cust->save();
                    
                    $addresses = $cust->getAddresses();
                    
                    foreach($addresses as $address){
                     $id =    $address->getId();
                    $address1 = $this->addressRepository->getById($id);
                    $address1->setFirstname($customersData[0]['customer']['firstname'])
                    ->setLastname($customersData[0]['customer']['lastname'])
                    ->setCountryId($customersData[0]['address']['countryid'])                  
                    ->setPostcode($customersData[0]['address']['postcode'])
                    ->setCity($customersData[0]['address']['city'])
                    ->setTelephone($customersData[0]['address']['phone'])
                    ->setFax($customersData[0]['address']['fax'])
                    ->setCompany($customersData[0]['address']['company'])                  
                    ->setStreet($customersData[0]['address']['street'])
                   ->setIsDefaultBilling('1')
                   ->setIsDefaultShipping('1');
                    
                   if(isset($data['address']['state']) && $data['address']['state']!=''){
                       $address1->setRegionId($customersData[0]['address']['state']);
                   }
                   
                    $this->addressRepository->save($address1);
                    }
                                     
                }
                
                return json_encode(array('code'=>'error','message'=>'Customer Exists'));
            } 
        }
        
       $i=0;
       $x=array();
       $existdatas = array();
       
        foreach($customersData as $data){
            
            $check[$data['customer']['source_sys_uid']]=0;
            
            $customer   = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            
            $this->collections = clone $this->collection;
           
            $exist_emails[$data['customer']['source_sys_uid']]=$this->collections
            ->addFieldToFilter('email', array('eq' => $data['customer']['email']))
            ->addFieldToFilter('source_sys_uid',array('eq' => $data['customer']['source_sys_uid']));
            
            $checked_data[$data['customer']['source_sys_uid']]=$exist_emails[$data['customer']['source_sys_uid']]->getData();
            
          
       
            if($checked_data[$data['customer']['source_sys_uid']]){             
                $existdatas[$data['customer']['source_sys_uid']]=$checked_data[$data['customer']['source_sys_uid']][0]['entity_id'];
          
            } else {
                $existdatas[$data['customer']['source_sys_uid']]=0;
           
            }
            
          
           
            if($existdatas[$data['customer']['source_sys_uid']]){
            
               $x[$i]['code'] = 'Error';
               $x[$i]['message'] = 'Customer Id: '.$data['customer']['source_sys_uid'].' exists';
               $check[$data['customer']['source_sys_uid']]=1;
               unset($existdatas);
               unset($checked_data);
               $i++;              
            } else{
                $check[$data['customer']['source_sys_uid']]=0;
             
            }
            
           
          
            if($check[$data['customer']['source_sys_uid']]==0){
        try {
            
            if(!isset($data['customer']['product_type'])){
                $data['customer']['product_type'] = $data['customer']['product_types'];
            }
            // Preparing data for new customer
            $customer->setEmail($data['customer']['email']);
            $customer->setFirstname($data['customer']['firstname']);
            $customer->setLastname($data['customer']['lastname']);
            $customer->setPassword($data['customer']['password']);
            
        
                $customer->setData("username",$data['customer']['source_sys_uid']);
                $customer->setData("association_name",$data['customer']['association_name']);
                $customer->setData("association_id",$data['customer']['association_id']);
                $customer->setData("product_type",$data['customer']['product_type']);
                $customer->setData("source_sys_uid",$data['customer']['source_sys_uid']);
       
                      
            
            if(isset($data['customer']['config_url'])){
            $customer->setData("config_url",$data['customer']['config_url']);
            }
                     
            
            try{
            $customer->save();
            
            $tokens = $this->tokenFactory->create();
            $tokens = $tokens->load($bearer_access_token,'token');
            
            $findtoken = $tokens->getToken();
            
            if($findtoken){
                $customerID = $customer->getId();
                $naylorobj=$this->NaylorloginsFactory->create();
                $naylorobj->setAccessToken($findtoken);
                $naylorobj->setCustomerId($customerID);
                $naylorobj->setAssociationId($data['customer']['association_id']);
                $naylorobj->setNaylorEmail($data['customer']['email']);
                $naylorobj->setCreatedAt(time());
                $naylorobj->save();
            }
            
           
            } catch(Exception $e){
                echo $e->getMessage();
            }
            
            $directoryList = $this->directoryList;
            $file = $this->file;
            
            $tmpDir = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'customer';
            
            $customerID = $customer->getId();
            
            
                      
            $customerObj = $this->_customerRepoInterface->getById($customerID, $websiteId);
            
            
           if(isset($data['customer']['image_url'])){
                $imageUrl = $data['customer']['image_url'];
            } else {
                $imageUrl = '';
            }
          
        
            $customerObj->setCustomAttribute('username',$data['customer']['source_sys_uid']);
            $customerObj->setCustomAttribute('association_name',$data['customer']['association_name']);
            $customerObj->setCustomAttribute('association_id',$data['customer']['association_id']);
            $customerObj->setCustomAttribute('product_type',$data['customer']['product_type']);
            $customerObj->setCustomAttribute('source_sys_uid',$data['customer']['source_sys_uid']);
            
            
            if($imageUrl!=''){
            $file->checkAndCreateFolder($tmpDir . '/'.$customerID);
            $newFileName = $tmpDir . '/'.$customerID.'/'.baseName($imageUrl);
            
            
            $result = $file->read($imageUrl, $newFileName);
            
            
             $customerObj->setCustomAttribute('profile_picture',$customerID.'/'.baseName($imageUrl));
            
               
             
             $this->_customerRepoInterface->save($customerObj);
            }
            
            $addresss = $this->addressFactory;
            $address = $addresss->create();
            
            $address->setCustomerId($customerID)
            ->setFirstname($data['customer']['firstname'])
            ->setLastname($data['customer']['lastname'])
            ->setCountryId($data['address']['countryid'])           
            ->setPostcode($data['address']['postcode'])
            ->setCity($data['address']['city'])
            ->setTelephone($data['address']['phone'])
            ->setFax($data['address']['fax'])
            ->setCompany($data['address']['company'])
            ->setStreet($data['address']['street'])
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1');
            
            if(isset($data['address']['state'])){
              $address->setRegionId($data['address']['state']);
            }
            try{
                $address->save();
                $customer->unsetData();
                $address->unsetData();
            }
            catch(Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
            
         
       
        } catch(Exception $e){
            print $e->getMessage();
        }
        
         }
        
       
        unset($data);
    }
    if(count($x)>0){
        return json_encode($x);
    } else {
      return json_encode(array('code'=>'success','message'=>'Successfully created the customers'));
    }
    }
}