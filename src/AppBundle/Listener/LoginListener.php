<?php

namespace AppBundle\Listener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginListener implements EventSubscriberInterface{
    private $router;

    public function __construct(UrlGeneratorInterface $router){
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(){
        return array(
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onPasswordResettingSuccess',
        );
    }

    public function onPasswordResettingSuccess(FormEvent $event){
//        $url = $this->router->generate('favoris');
//
//        $event->setResponse(new RedirectResponse($url));
    }
}