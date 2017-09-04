<?php
/**
 * Mail Transport
 */
namespace Mycompany\Webservice\Model;

class Transport extends \Zend_Mail_Transport_Smtp implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var \Magento\Framework\Mail\MessageInterface
     */
    protected $_message;
    
    /**
     * @param MessageInterface $message
     * @param null $parameters
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\Framework\Mail\MessageInterface $message,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
        )
    {
        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }
        
        $this->_scopeConfig=$scopeConfig;
        
        $smtpHost= $this->_scopeConfig->getValue('service_config/emailconfig/host', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        $smtpConf = [
            'auth' => $this->_scopeConfig->getValue('service_config/emailconfig/authtype', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'ssl' => $this->_scopeConfig->getValue('service_config/emailconfig/ssl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'port' => $this->_scopeConfig->getValue('service_config/emailconfig/port', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'username' => $this->_scopeConfig->getValue('service_config/emailconfig/username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'password' => $this->_scopeConfig->getValue('service_config/emailconfig/password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ];
        
        parent::__construct($smtpHost, $smtpConf);
        $this->_message = $message;
    }
    
    /**
     * Send a mail using this transport
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        try {
            parent::send($this->_message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }
}
