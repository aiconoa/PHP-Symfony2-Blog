<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \AppBundle\Form\Type\ArticleType;
use \AppBundle\Entity\Article;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Description of ArticleController
 *
 * @author T
 */
class ArticleController extends Controller {
    const NB_ARTICLE_PER_PAGE = 10;

    /**
     * @Route("/list")
     */
    public function listAction(Request $request) {
        $page =  $request->query->get('page', '1');
        
        if (! ctype_digit($page)) {
            throw $this->createNotFoundException("La page n'existe pas"); // 404 - la page n'existe pas
        }
        
        $page = (int) $page;
        
        if ($page < 1) {
            throw $this->createNotFoundException("La page n'existe pas"); // 404 - la page n'existe pas
        }
        
        $repository = $this->getDoctrine()->getRepository("AppBundle\Entity\Article");
        $totalArticles = $repository->count();
        $pageMax = ceil($totalArticles / ArticleController::NB_ARTICLE_PER_PAGE);

        if ($page > $pageMax) {
            throw $this->createNotFoundException("La page n'existe pas"); // 404 - la page n'existe pas
        }
        
        $offset = ($page - 1) * ArticleController::NB_ARTICLE_PER_PAGE;
        
       
//        $article1 = new Article();
//        $article1->setId(1);
//        $article1->setTitle("Le titre de mon premier article");
//        $article1->setContent("Le contenu de mon premier article");
//        
//        $article2 = new Article();
//        $article2->setId(2);
//        $article2->setTitle("Le titre de mon deuxième article");
//        $article2->setContent("Le contenu de mon deuxième article");
        
//        $articles = [$article1, $article2];
        

        //$articles = $repository->findBy(array(), null, ArticleController::NB_ARTICLE_PER_PAGE, $offset);
        $articles = $repository->findWithAuthorByOffsetAndLimit($offset,ArticleController::NB_ARTICLE_PER_PAGE);
        
        dump($articles);
        return $this->render('article/list.html.twig', array('articles' => $articles,
                                                             'page'     => $page,
                                                             'pageMax'  => $pageMax));
    }
    
    /**
     * @Route("/show/{id}", requirements={"id" = "\d+"})
     */
    public function showAction($id) {
         // dump($id);
//        $article1 = new Article();
//        $article1->setId(1);
//        $article1->setTitle("Le titre de mon premier article");
//        $article1->setContent("Le contenu de mon premier article");
        
        $repository = $this->getDoctrine()->getRepository("AppBundle\Entity\Article");
        $article = $repository->find($id);

        if (false === $this->get('security.authorization_checker')->isGranted('view', $article)) {
            throw new AccessDeniedException('Unauthorised access!');
        }
        
        if ($article == null) {
            throw $this->createNotFoundException("L'article n'existe pas");
        }
        
        return $this->render('article/show.html.twig', array('article' => $article));
    }
    
    /**
     * @Route("/create")
     */
    public function createAction(Request $request) {

        if ($this->getUser() == null) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $article = new Article();

        $form = $this->createForm(new ArticleType(), $article);
        
        $form->handleRequest($request);
        
        if($form->isValid()) {

            $article->setAuthor($this->getUser());

            // sauvegarder dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            
            // rediriger vers la page d'accueil
            return $this->redirectToRoute('app_article_list');
        }
        
        return $this->render('article/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/delete/{id}", requirements={"id" = "\d+"})
     */
    public function deleteAction($id) {

        $repository = $this->getDoctrine()->getRepository("AppBundle\Entity\Article");
        $article = $repository->find($id);

        if ($article == null) {
            throw $this->createNotFoundException("L'article n'existe pas");
        }

        if (false === $this->get('security.authorization_checker')->isGranted('delete', $article)) {
            throw new AccessDeniedException('Unauthorised access!');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($article);
        $em->flush();
        return $this->redirectToRoute('app_profile_index', ['username' =>  $this->getUser()->getUsername()]);
    }
    
    /**
     * @Route("/edit/{id}", requirements={"id" = "\d+"})
     * @param type $id
     */
    public function editAction(Request $request, $id) {
        $repository = $this->getDoctrine()->getRepository("AppBundle\Entity\Article");
        $article = $repository->find($id);
        
        if (false === $this->get('security.authorization_checker')->isGranted('edit', $article)) {
            throw $this->createAccessDeniedException('Unauthorised access!');
        }
        
        if ($article == null) {
            throw $this->createNotFoundException("L'article n'existe pas");
        }
        
        $form = $this->createForm(new ArticleType(), $article);
        
        $form->handleRequest($request);
        
        if($form->isValid()) {
            // sauvegarder dans la BDD
            $em = $this->getDoctrine()->getManager();
            $em->merge($article);
            $em->flush();
            
            // rediriger vers la page d'accueil
            return $this->redirectToRoute('app_article_list');
        }
        
        return $this->render('article/edit.html.twig', array('form' => $form->createView()));
    }
    
    public function latestNArticlesAction($n) {
        $repository = $this->getDoctrine()->getRepository("AppBundle\Entity\Article");
        $articles = $repository->findWithAuthorByOffsetAndLimit(0,$n);
        
        return $this->render('article/latestNarticles.html.twig', array('articles' => $articles));
    }
}
