<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        // générer une Response
        // return new Response("hello world !");
        return $this->render('default/index.html.twig');
    }
    
    /**
     * @Route("/about")
     */
    public function aboutAction() {
//        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
//            throw $this->createAccessDeniedException();
//        }

        return $this->render('default/about.html.twig');
    }
    

    /**
     * @Route("/api/date")
     */
    public function dateAction() {   
        return new \Symfony\Component\HttpFoundation\JsonResponse(new \DateTime());
    }
    
}
