<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ProfileController extends Controller
{
    /**
     * @Route("/profile/{username}")
     */
    public function indexAction($username)
    {

        // logged user ?
        // $user = $this->get('security.token_storage')->getToken()->getUser(); //cf below for shortcut

        $repository = $this->getDoctrine()->getRepository("AppBundle\Entity\Article");
        $articles = $repository->findByAuthor($this->getUser());
        return $this->render('profile/index.html.twig', array('articles' => $articles));
    }

}
