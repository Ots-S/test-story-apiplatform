<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Story;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

final class NewStorySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['generateEan', EventPriorities::PRE_WRITE],
        ];
    }

    public function generateEan(ViewEvent $event): void
    {
        $story = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$story instanceof Story || Request::METHOD_POST !== $method) {
            return;
        }

        if(empty($story->getEan())) {
            $story->setEan($this->generateRandomEan());
        }
    }

    public function generateRandomEan(): string {
        $numbers = '0123456789';
        $length = strlen($numbers);
        $randomEan = '';
        for ($i = 0; $i < 13; $i++) {
            $randomEan .= $numbers[rand(0, $length - 1)];
        }
        return $randomEan;
    }
}