<?php
namespace Yfret\EventsTracker\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
    
class AddToWishlist implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        echo "Hello wishlisted product";
        $products = $observer->getItems();
        if($products)
        {
            echo 'There';
            echo count($products);
            echo "products";
            echo json_encode($products);
            foreach($products as $product)
            {
                echo "Product ID";
                echo $product->getProduct()->getId();
            }
        }
        else
        {
            echo 'Not there';
        }
        throw new Exception("sdjb");
        $product_id = $product->getId();

        $url = "http://localhost:8000/event/?access_key=8ef315a17a7e45fd8fe3116ada473c9f&object_id=" . $product_id . "&action_type=add_to_cart&object_type=product";

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
}