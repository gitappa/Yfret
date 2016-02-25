<?php
namespace Yfret\EventsTracker\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
    
class UserSignup implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        try
        {
            $customer = $observer->getEvent()->getCustomer();
            $fname =  $customer->getFirstName();
            $email = $customer->getEmail();
            $url = 'http://preprod.yfret.com/event/?name=' . $fname . '&user_id=' . $email . '&object_id=' . $email . '&email=' . $email . '&action_type=register&user_type=email&object_type=user';

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $urlInterface = $objectManager->get('Magento\Framework\UrlInterface');
            $url = $url . "&og:url=" . $urlInterface->getCurrentUrl();
            
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