<?php
namespace Yfret\EventsTracker\Observer;

use \Magento\Framework\Event\ObserverInterface;

echo 'Go world';
class CemEventsTracker implements ObserverInterface
{
    public function addToCart(\Magento\Event\Observer $observer)
    {
        echo 'Hello World';
    }

    public function viewProduct(\Magento\Event\Observer $observer)
    {
        echo 'Hello World when viewed';
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        echo 'In execute, saying hello world';
    }
}