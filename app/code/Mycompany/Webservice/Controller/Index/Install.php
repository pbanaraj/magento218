<?php
/**
 * Install  controller to make ajax call
 * @category Mycompany
 * @package  Mycompany_Webservice
 * @module   Webservice
 * @author   Tavant Developer
 */

namespace Mycompany\Webservice\Controller\Index;
use Magento\Framework\Controller\ResultFactory;

/**
 * @var Mycompany\Webservice\Controller\Index\Install
 * @author tavant
 *
 */
class Install extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $resultRawFactory;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Mycompany\Webservice\Helper\Data $service_helper,
        \Mycompany\Webservice\Model\NaylorinstallsFactory $installFactory,
        \Magento\Customer\Model\Session $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mycompany\Webservice\Model\Adapter\Rest $rest,
        \Mycompany\Webservice\Model\NaylorinstallsFactory $saveFactory,
        array $data = []
        )
    {
        
        $this->resultLayoutFactory = $resultFactory;
        $this->resultRawFactory    = $jsonResultFactory;
        $this->request = $request;
        $this->service_helper = $service_helper;
        $this->installFactory = $installFactory;
        $this->_session = $session;
        $this->_storeManager = $storeManager;
        $this->rest = $rest;
        $this->_saveFactory = $saveFactory;
        parent::__construct($context);
    }
    
    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
             
        
        $resultLayout = $this->resultLayoutFactory->create();
        $resultJson = $this->resultRawFactory->create();
        
        $blockObj= $resultLayout->getLayout()->createBlock('Mycompany\Webservice\Block\Sidemenu');
        
        $sku =  $this->request->getPost('sku');
       
        if($this->_session->isLoggedIn()) {
               
        $inst = $this->installFactory->create();
        $inst->setSku($sku);
        $inst->setAssociationId($this->_session->getCustomer()->getAssociationId());
        $inst->setCreatedAt(time());
        $inst->setCustomerId($this->_session->getCustomer()->getId());
        $inst->setNaylorEmail($this->_session->getCustomer()->getEmail());
        $inst->setStore($this->_storeManager->getStore()->getStoreId());
        $inst->setWebsiteId($this->_storeManager->getStore()->getWebsiteId());
        
        
        $msg = array();
        $msg = $this->rest->setActiveFeatures($this->_session->getCustomer()->getAssociationId(),$sku);
        $error = 1;
        
        foreach($msg as $m){
            if(strtolower($m) == $sku){
                $error = 0;
            }
        }
        
        // will comment out $error == 0 if not needed
        if(!empty($msg) && $error == 0){           
            
            $msg = json_encode($msg);
            $inst->setRunResult($msg);
        } else {
            $msg = array();
            $msg['error'] = $error;
            $msg['message'] = "Error Install Failed";
            $msg = json_encode($msg);
        }
                
        
        $inst->save();
        
        $resultJson->setData($msg);
        return $resultJson;
        } else {
            
            $msg['error'] = 1;
            $msg['message'] = "Login session expired, Please login.";
            $resultJson->setData(json_encode($msg));
            return $resultJson;
            
        }
        
        
        
       
        
       /* $resultJson = $this->resultRawFactory->create();
        
        if($blockObj) {
        
             $sku =  $this->request->getPost('sku');
            
            
            $assoc_id = $blockObj->getLoggedinUsers();
            
            $msg = $blockObj->setActiveFeatures($assoc_id,$sku);
          
           
           $orders = $this->service_helper->createOrderWithInvoice($sku);         
           $msg->order_id = $orders['order_id'];
           $resultJson->setData(json_encode($msg));
            return $resultJson;
            
            
        } else {
            $msg['error'] = 1;
            $resultJson->setData(json_encode($msg));
            return $resultJson;
        }        */
        
        
    }
}

