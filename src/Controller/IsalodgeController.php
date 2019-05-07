<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IsalodgeController extends AbstractController
{
    /**
     * @Route("/isalodge", name="isalodge")
     */
    public function index(ArticleRepository $repo)
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();

        return $this->render('isalodge/index.html.twig', [
            'controller_name' => 'IsalodgeController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('isalodge/home.html.twig');
    }

    /**
     * @Route("/isalodge/new", name="isalodge_create")
     * @Route("/isalodge/{id}/edit", name="isalodge_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager)
    {   
        if(!$article){
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) 
        {
            if(!$article->getId())
            {
                $article->setCreatedAt(new \DateTime());

                $manager->persist($article);
                $manager->flush();

            }
           
            return $this->redirectToRoute('isalodge_show', ['id' => $article->getId()]);

        }

        return $this->render('isalodge/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);

    }
    /**
     * @Route("/isalodge/{id}", name= "isalodge_show")
     */
    public function show(Article $article, Request $request, ObjectManager $manager)
    {
        $comment =new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('isalodge_show', ['id' => $article->getId()]);
        }
        return $this->render('isalodge/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);

    }
    
}