<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent as InteractiveLoginEventAlias;

class LoginListener {

    protected $session;
    protected $em;

    public function __construct(EntityManager $entityManager, Session $session){
        $this->em = $entityManager;
        $this->session = $session;
    }


    /**
     * @param InteractiveLoginEventAlias $event
     * @throws Exception
     */
    public function onInteractiveLogin(InteractiveLoginEventAlias $event){
        $username = $event->getAuthenticationToken()->getUsername();

        /** @var User $databaseUser */
        $databaseUser = $this->em->getRepository('AppBundle:User')
            ->findOneBy(["username" => $username]);

        if ($databaseUser !== null && $databaseUser->isEnabled()) {

            if ($this->session->get('liste_fav')) {
                // some actions ...
            }
        }
    }

    public function onLoginError(AuthenticationEvent $event){
        // Login error
    }
}