<?php
namespace Eighteentech\SendMail\Helper;

use Magento\Framework\Url\DecoderInterface;
use Magento\Framework\Url\EncoderInterface;
/**
 * helper class.
 */
class Helper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @param EncoderInterface $urlEncoder
     * @param DecoderInterface $urlDecoder
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     */
    public function __construct(
        EncoderInterface $urlEncoder,
        DecoderInterface $urlDecoder,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        $this->urlEncoder = $urlEncoder;
        $this->urlDecoder = $urlDecoder;
        $this->encryptor = $encryptor;
        
        parent::__construct($context);
    }
    
    /**
     * @param $url
     * @return string
     */
    public function encodeUrl($url)
    {
        return $this->urlEncoder->encode($url);
    }
 
    /**
     * @param $url
     * @return string
     */
    public function decodeUrl($url)
    {
        return $this->urlDecoder->decode($url);
    }
    
    /**
     * Encrypt string
     *
     * @param   string $str
     * @return  string
     */
    public function encryptString($str)
    {
        return $this->encryptor->encrypt($str);
    }
    /**
     * Decrypt string
     *
     * @param   string $str
     * @return  string
     */
    public function decryptString($str)
    {
        return $this->encryptor->decrypt($str);
    }
}
