<?php

/**
 *  Login Controller to implement custom login
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservice
 * @author Tavant Developer
 */

namespace Mycompany\Webservice\Controller\Account;


use Magento\Framework\Controller\ResultFactory;

/**
 * 
 * @var Mycompany\Webservice\Controller\Account\Login
 * @author tavant
 *
 */

class Login extends \Mycompany\Webservice\Controller\Accounts
{

    protected $mpHelper;
    protected $object_manager;
    protected $resultFactory;

    /**
     * @var \Diglin\Github\Model\Adapter\Oauth2Factory
     */
    protected $mpAdapterOauth2Factory;
    
    protected $mathRandom;
    
    protected $customerFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Mycompany\Webservice\Model\Adapter\JWT $jwtobj,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Mycompany\Webservice\Helper\Data $mpHelper,
        \Mycompany\Webservice\Model\Adapter\Oauth2Factory $oauthfactory,
        \Magento\Framework\Math\Random $mathRandom,
        \Mycompany\Webservice\Model\NaylorloginsFactory $NaylorloginsFactory,
        \Mycompany\Webservice\Model\ResourceModel\Naylorlogins\CollectionFactory  $naylorColelctionFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $custfactory,
        \Magento\Integration\Model\Oauth\TokenFactory $tokenFactory,
        \Magento\Integration\Model\AdminTokenServiceFactory  $IntegrationFactory,
        \Magento\Integration\Helper\Oauth\Data $dataHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        
        
        $this->resultFactory = $resultPageFactory;
        $this->mathRandom = $mathRandom;
        $this->mpHelper = $mpHelper;
        $this->mpAdapterOauth2Factory = $oauthfactory;
        
        $this->NaylorloginsFactory = $NaylorloginsFactory;
        $this->naylorColelctionFactory = $naylorColelctionFactory;
        $this->request = $request;
        $this->jwt = new $jwtobj;
        $this->resultRawFactory    = $jsonResultFactory;
        $this->customerFactory = $custfactory;
        $this->resultFactory = $resultPageFactory;
        $this->tokenFactory = $tokenFactory;
        $this->IntegrationFactory = $IntegrationFactory;
        $this->dataHelper = $dataHelper;
             
        $this->config = $scopeConfig;
        parent::__construct($context,$resultPageFactory,$customerSession);
        
       
    }
    public function execute ()
    {
        
       $token =  $this->request->getParam('code');
        
       
        
        
        
        
        if(!isset($token) && $token == ''){
          
         
           
          $post_data = $this->request->getContent();
   
            $post = (array)json_decode($post_data);
            
            
            if(!$post_data){
                $resultJson = $this->resultRawFactory->create();
                $resultJson->setData('{"error": "forbidden", "message": "1.Invalid request!"}');
                return $resultJson;
            } else if(empty($post)){
                $resultJson = $this->resultRawFactory->create();
                $resultJson->setData('{ "error": "forbidden", "message": "2.Invalid request!" }');
                return $resultJson;
           }
            
        
        $request_headers['Content-Type'] = "application/json"; 
        $request_headers['Content-Lenght'] = strlen(json_encode($post)); 
        $oauth = $this->mpAdapterOauth2Factory->create();
        $response = $oauth->executeRequest($oauth::TOKEN_ENDPOINT,$post,'POST',$request_headers);
        
        if($response['code']!=200){
            // echo '{ "error": "forbidden", "message": "Invalid request!" }' ;
            $resultJson = $this->resultRawFactory->create();
            $resultJson->setData('{ "error": "forbidden", "message": "3. Invalid request!" }');
            return $resultJson;
        } else {
            
            $naylorobj=$this->NaylorloginsFactory->create();           
            $naylorobj->setAccessToken($response['result']);
            $naylorobj->setCreatedAt(time());
            $naylorobj->save();
            
            $resultJson = $this->resultRawFactory->create();
            $resultJson->setData($response);
            
            return $resultJson;
            
            
            /*
            $collection=$this->naylorColelctionFactory->create();
           
            $collection->addFieldToFilter('access_token','mlsrg6s12c6ghcs5t4sv4osxwmdikj2s')
           ->addFieldToFilter('id',1);
           */
           
          
           /* print $collection->load()->getFirstItem()->getCustomerId();
            print_r($collection->getData());  */ 
            
           
            
        }
        
        } else {
            
            /*$key = $this->config->getValue('service_config/general/naylor_jwt_key');
            
            if($key == ''){
                $key = "DontAsk";
            }*/
            
            
                        
            // $response =  $this->jwt->decode($token, $key, $algo = 'HS256');
            
            $encryption_key = $this->config->getValue('service_config/general/naylor_enc_key');
            
            if($encryption_key == ''){
                $encryption_key = "DontAsk000000000";
            }
            
          
         
            $response = $this->jwt->Decrypt128($token,$encryption_key);
          
        
            
          
           $customer = $this->customerFactory->create();
        
            $customer
            ->addAttributeToFilter('association_id',$response->association_id)
            ->addAttributeToFilter('email',$response->email_id);
            $customerdata = $customer->load()->getFirstItem();
                       
            
            $cust_id = $customerdata->getId();
            
           /*
            $nlogins = $this->NaylorloginsFactory->create();
            $nlogsin = $nlogins->load($response->access_token,'access_token');
            
            $findtoken = $nlogsin->getAccessToken();
        
           echo $response->access_token;
           die();
          */
            $tokens = $this->tokenFactory->create();
            $tokens = $tokens->load($response->access_token,'token');
            $findtoken = $tokens->getToken();
           
            
            if($findtoken && $findtoken == $response->access_token){
            // saving details of cutomers to know access token used by them.
            $nlogsin = $this->NaylorloginsFactory->create();                
            $nlogsin->setNaylorEmail($response->email_id);
            $nlogsin->setAssociationId($response->association_id);
            $nlogsin->setCustomerId($cust_id);
            $nlogsin->setAccessToken($response->access_token);
            $nlogsin->setCreatedAt(time());
            $nlogsin->save();
            
            $this->_getSession()->setCustomerAsLoggedIn($customerdata);
            
            
            $expiry = $this->dataHelper->getConsumerExpirationPeriod();
            $this->_dateHelper = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
            
            $currentTimestamp = $this->_dateHelper->gmtTimestamp();
           
            $updatedTimestamp = $this->_dateHelper->gmtTimestamp($tokens->getCreatedAt());
     
            if($expiry < ($currentTimestamp - $updatedTimestamp)){
             $this->IntegrationFactory->create()->revokeAdminAccessToken($tokens->getAdminId());
            } 
            
           
            
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('/');
             return $resultRedirect;
            } else {
                $resultJson = $this->resultRawFactory->create();
                $resultJson->setData('{ "error": "forbidden", "message": "Invalid request!" }');
                return $resultJson;
              
            }
            
        }
        
      
        
      
    }

    }
