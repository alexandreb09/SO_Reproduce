<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller{

    /**
     * @Route("/", name="Homepage")
     * @Route("/home", name="Homepage")
     * @return Response
     */
    public function carteAction(){
        $connected = $this->container->get('security.authorization_checker')
            ->isGranted('IS_AUTHENTICATED_REMEMBERED');

        return $this->render('@App/homepage.html.twig', array(
            "connected" => $connected));
    }
}
