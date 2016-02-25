<?php
namespace Yfret\EventsTracker\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
    
class PlaceOrder implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        try
        {
            $url = "http://preprod.yfret.com/event/?action_type=place_order&object_type=product";

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $urlInterface = $objectManager->get('Magento\Framework\UrlInterface');
            $url = $url . "&og:url=" . $urlInterface->getCurrentUrl();
            $customerSession = $objectManager->get('Magento\Customer\Model\Session');

            $orderIds = $observer->getEvent()->getOrderIds();
            if (empty($orderIds) || !is_array($orderIds)) {
                return;
            }
            $order_id = array_pop($orderIds);
            $url = $url . "&object_id=" . $order_id;

            $order = $objectManager->get('Magento\Sales\Model\Order');
            $order_information = $order->load($order_id);
            $orderItems = $order_information->getAllVisibleItems();
            $orders = array();
            foreach($orderItems as $item)
            {
                $product_id = $item->getSku();
                $qty = intval($item->getQtyOrdered());

                $product = array("id" => strval($product_id), "qty" => strval($qty));
                array_push($orders, $product);
            }
            $meta = array("orders" => $orders);
            $url = $url . "&meta=" . json_encode($meta);

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