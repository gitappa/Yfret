<?php
namespace Yfret\EventsTracker\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
    
class AddToCart implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        try
        {
            $product = $observer->getProduct();
            $product_id = $product->getSku();

            $url = "http://preprod.yfret.com/event/?object_id=" . $product_id . "&action_type=add_to_cart&object_type=product";

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $urlInterface = $objectManager->get('Magento\Framework\UrlInterface');
            $url = $url . "&og:url=" . $urlInterface->getCurrentUrl();
            $customerSession = $objectManager->get('Magento\Customer\Model\Session');

            if($customerSession->isLoggedIn())
            {
                $customer = $customerSession->getCustomer();
                $email = $customer->getEmail();
                $url = $url . "&user_id=" . $email . "&user_type=email";
            }
            else
            {
                $url = $url . "&user_id=&user_type=guest";
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $content = curl_exec($ch);
            curl_close($ch);
        }
        catch (Exception $e)
        {
            //skip the event
        }
    }
}