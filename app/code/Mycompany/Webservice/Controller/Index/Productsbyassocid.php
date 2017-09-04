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
class Productsbyassocid extends \Magento\Framework\App\Action\Action
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
        array $data = []
        )
    {
        
        $this->resultLayoutFactory = $resultFactory;
        $this->resultRawFactory    = $jsonResultFactory;
        $this->request = $request;
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
        
        
        $blockObj= $resultLayout->getLayout()->createBlock('Mycompany\Webservice\Block\Sidemenu');
        
        $resultJson = $this->resultRawFactory->create();
        
        if($blockObj) {
            $sku =  $this->request->getPost('sku');
            
            
            $assoc_id = $blockObj->getLoggedinUsers();
            
            $msg = $blockObj->getProductDetails($assoc_id,$sku);
            
            
            $resultJson->setData(json_encode($msg));
            return $resultJson;
            
            
        } else {
            $msg['error'] = 1;
            $resultJson->setData(json_encode($msg));
            return $resultJson;
        }
        
        
        
    }
}


