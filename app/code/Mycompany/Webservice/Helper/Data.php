<?php
/**
 *  Data helper class to implement custom Login
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservicevar
 * @author Tavant Developer
 */

namespace Mycompany\Webservice\Helper;

use Magento\Framework\ObjectManagerInterface;
use Mycompany\Webservice\Controller\Index\Install;

/**
 * 
 * @var Mycompany\Webservice\Helper\Data
 * @author tavant
 *
 */

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Session $session
     * @param ObjectManagerInterface $om
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $product
     * @param \Magento\Framework\Data\Form\FormKey $formkey
     * @param \Magento\Quote\Model\QuoteFactory $quote
     * @param \Magento\Quote\Model\QuoteManagement $quoteManagement
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Sales\Model\Service\OrderService $orderService
     * @param \Magento\Customer\Model\SessionFactory $loggedinsession
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagementInterface
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Quote\Model\Quote\Address\Rate $shippingRate
     */
   

    public function __construct(     
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Session $session,
        ObjectManagerInterface $om,      
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Sales\Model\Service\OrderService $orderService, 
        \Magento\Customer\Model\SessionFactory $loggedinsession,
        \Magento\Directory\Model\CountryFactory $countryFactory,     
 
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Quote\Model\Quote\Address\Rate $shippingRate,
        \Magento\Store\Model\StoreManagerInterfaceFactory $storeFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Service\InvoiceServiceFactory $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transaction,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSenderFactory $sender
    ) {
        
        $this->object_manager = $om;
       //  $this->scopeConfig = $context->getScopeConfig();
        $this->session = $session;
        
        $this->_storeManager = $storeManager;
        $this->_product = $product;
        $this->_formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->shippingRate = $shippingRate;
        
        $this->customer_session = $loggedinsession;
        $this->addressRepository = $addressRepository;
        $this->countryFactory = $countryFactory;
        
        $this->orderRepository = $orderRepository;
        $this->_storeFactory = $storeFactory;
        
        $this->_orderFactory = $orderFactory;
        $this->_invoiceService  = $invoiceService;
        
        $this->_transaction = $transaction;
        $this->_invoiceSender = $sender;
       
        
        parent::__construct($context);

    }
    
    
    /**
     * to create order
     * @see Mycompany\Webservice\Controller\Index\Install 
     * @param unknown $sku
     * @return string|number[]|string[]|number
     */
    
    public function createOrderWithInvoice($order = array()){
        
         
            
            $websiteid = $order['website_id'];
            $store = $this->_storeFactory->create()->getStore($order['store_id']);
            $email = $order['email'];
            $customer_id = $order['customer_id'];
            $association_id = $order['assoc_id'];
           
            
            
            
            
            $customerAddress = array();
            $customerObj = $this->customerRepository->getById($customer_id);
            
            
            
            
            $billingAddress = $customerObj->getDefaultBilling();
            $shipAddress = $customerObj->getDefaultShipping();
            
            $address = $this->addressRepository->getById($billingAddress);
            
            
            
            $address1['street'] = $address->getStreet();
            $street = $address1['street'][0];
            
            if(isset($address1['street'][1])){
                $street.=$address1['street'][1];
            }
            
            $orders=array(
                'currency_id'  => 'USD',
                'email'        => $email,
                'shipping_address' => array(
                    'firstname'    => $address->getFirstName(), //address Details
                    'lastname'     => $address->getLastName(),
                    'street' => $street,
                    'city' =>$address->getCity(),
                    'country_id' => $address->getCountryId(),
                    'postcode' => $address->getPostcode(),
                    'telephone' => $address->getTelephone(),
                    'save_in_address_book' => 1
                )
            );
            
            
           //  $store=$this->_storeManager->getStore();
            $customer=$this->customerFactory->create();
            $customer->setWebsiteId($websiteid);
            $customer->load($customer_id);
            
            $cart_id = $this->cartManagementInterface->createEmptyCart();
            $cart = $this->cartRepositoryInterface->get($cart_id);
            $cart->setStore($store);
            
            
            
            $customer= $this->customerRepository->getById($customer_id);
            
            $cart->assignCustomer($customer);
            
            
            $product = $this->_product->create();
            
            $product = $product->load($product->getIdBySku($order['sku']));
            
            $product->setPrice($product->getFinalPrice());
            $cart->addProduct($product,1);
            
            
            $cart->getBillingAddress()->addData($orders['shipping_address']);
            $cart->getShippingAddress()->addData($orders['shipping_address']);
            
            
            
            $shippingAddress=$cart->getShippingAddress();
            $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('freeshipping_freeshipping');
            $cart->setPaymentMethod('checkmo');
            $cart->setInventoryProcessed(false);
            $cart->save();
            
            
            $cart->getPayment()->importData(['method' => 'purchaseorder']);
            
            
            $cart->collectTotals()->save();
            
            $cart->save();
            $cart = $this->cartRepositoryInterface->get($cart->getId());
            $order_id = $this->cartManagementInterface->placeOrder($cart->getId());
            
            
            
            if($order_id){
                $order = $this->orderRepository->get($order_id);
                $orderIncrementId = $order->getIncrementId();
                $invoice = $this->_invoiceService->create()->prepareInvoice($order);
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
                $invoice->register();
                
                $transaction = $this->_transaction->create()
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
                
                
                
                $transaction->save();
                
                // $this->invoiceSender->send($invoice);
                
                $orderState = 'complete';
                $order->setState($orderState,true)->setStatus($orderState);
                $order->addStatusToHistory($order->getStatus(), 'Order processed successfully with invoice: '.$invoice->getId());
                $order->addStatusHistoryComment(
                    __('Not Notified customer about invoice #%1.', $invoice->getId())
                    )
                    ->setIsCustomerNotified(false)
                    ->save();
                
                $result['order_id']= $orderIncrementId;
            } else{
                $result=array('error'=>1,'msg'=>'Order Creation Failed');
            }
            return $result;
            
       
        
        
    }
    
    public function createOrderWithInvoice1($sku){
        
        $customerSession = $this->customer_session->create();
        
        if($customerSession->isLoggedIn()){
            $customer_data=$customerSession->getCustomer();
            
            $websiteid = $customer_data->getWebsiteId();
            $storeid = $customer_data->getStoreId();
            $email = $customer_data->getEmail();
            $username = $customer_data->getUserName();
            $association_id = $customer_data->getAssociationId();
            
            $datapart=$customerSession->getCustomer()->getData();
            
            if(!isset($datapart['association_id'])){
                return 'Error:no association id';
            } 
            
          
            $customerAddress = array();
            $customerObj = $this->customerRepository->getById($customer_data->getId());
       
            
          
            
           $billingAddress = $customerObj->getDefaultBilling(); 
           $shipAddress = $customerObj->getDefaultShipping(); 
           
           $address = $this->addressRepository->getById($billingAddress);

           
           
           $address1['street'] = $address->getStreet();
           $street = $address1['street'][0];
           
           if($address1['street'][1]){
               $street.=$address1['street'][1];
           }
           
           $orders=array(
               'currency_id'  => 'USD',
               'email'        => $email,
               'shipping_address' => array(
                   'firstname'    => $address->getFirstName(), //address Details
                   'lastname'     => $address->getLastName(),
                   'street' => $street,
                   'city' =>$address->getCity(),
                   'country_id' => $address->getCountryId(),              
                   'postcode' => $address->getPostcode(),
                   'telephone' => $address->getTelephone(),                  
                   'save_in_address_book' => 1
               )
            );
           
           
           $store=$this->_storeManager->getStore();
           $customer=$this->customerFactory->create();
           $customer->setWebsiteId($websiteid);
           $customer->load($customer_data->getId());
           
           $cart_id = $this->cartManagementInterface->createEmptyCart();
           $cart = $this->cartRepositoryInterface->get($cart_id);
           $cart->setStore($store);
           
          
           
           $customer= $this->customerRepository->getById($customer_data->getId());
        
           $cart->assignCustomer($customer);
           
           
           $product = $this->_product->create();
         
           $product = $product->load($product->getIdBySku($sku));
           
           $product->setPrice($product->getFinalPrice());
           $cart->addProduct($product,1);
           
           
           $cart->getBillingAddress()->addData($orders['shipping_address']);
           $cart->getShippingAddress()->addData($orders['shipping_address']);
           
                 
           $shippingAddress=$cart->getShippingAddress();
           $shippingAddress->setCollectShippingRates(true)
           ->collectShippingRates()
           ->setShippingMethod('freeshipping_freeshipping'); 
           $cart->setPaymentMethod('checkmo'); 
           $cart->setInventoryProcessed(false); 
           $cart->save(); 
           
          
           $cart->getPayment()->importData(['method' => 'checkmo']);
           
         
           $cart->collectTotals()->save();
           
           $cart->save();
           $cart = $this->cartRepositoryInterface->get($cart->getId());
           $order_id = $this->cartManagementInterface->placeOrder($cart->getId());
           
                   
        
           if($order_id){
               $order = $this->orderRepository->get($order_id);
               $orderIncrementId = $order->getIncrementId();
               
               
               $result['order_id']= $orderIncrementId;
           } else{
               $result=array('error'=>1,'msg'=>'Order Creation Failed');
           }
           return $result;
            
        } else {
            $result=array('error'=>1,'msg'=>'Customer not logged in');
        }
            
        
      }

    
    public function getSession()
    {
        
        return $this->object_manager->get('Magento\Customer\Model\Customer\session');
    }

    /**
     *
     * @param string $token
     * @return string
     */
    public function setAccessToken ($token)
    {
        return $this->session->setData('marketplace_access_token', $token);
    }

    /**
     *
     * @return string
     */
    public function getAccessToken ()
    {
        return $this->session->getData('marketplace_access_token');
    }

    
    public function setUserData ($data)
    {
        return $this->session->setData('marketplace_data', $data);
    }

    /**
     * @see $this->setUserData() to see the data format returned
     *
     * @return array
     */
    public function getUserData ()
    {
        // return $this->create($this->session->getData('github_data'));
        
        $obj = new \Magento\Framework\DataObject();
        return $obj->setData($this->session->getData('marketplace_data'));
        
    }

    public function getCustomer($association_id,$email)
    {
        /* @var $resource Mage_Customer_Model_Resource_Customer_Collection */
        $resource = $this->object_manager->get('Magento\Customer\Model\ResourceModel\Customer\Collection');
       
        return $resource->addAttributeToFilter('association_id', $association_id)->addAttributeToFilter('email', $email)
            ->load()
            ->getFirstItem();
    }

    
}
