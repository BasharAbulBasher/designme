<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Services\ArticleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    private ArticleService $articleService;
    public function __construct(EntityManagerInterface $entityManagerEnterface)
    {
        $this->articleService=new ArticleService($entityManagerEnterface);
    }
    #[Route('/admin/article/create', name: 'article.create', methods:['GET','POST'])]
    public function create(EntityManagerInterface $entityManagerEnterface, Request $request): Response
    {
        //Get all Categories
        $categories=$entityManagerEnterface->getRepository(Category::class)->findAll();

        return $this->render('article/create.html.twig', [
            'categories' => $categories,
            'data'=>$request->get('data')
        ]);
    }
      #[Route('/admin/article/save', name: 'article.save', methods:['POST'])]
      public function save(Request $request)
      {
        $storage = $this->getParameter("kernel.project_dir") . "/public/img";
        $data=$this->articleService->save($request, $storage);
       return $this->redirectToRoute('article.create',[
            "data"=>$data,
        ]);
      }
      #[Route('/admin/article/show', name:'article.show' , methods:['GET'])]
      public function show()
      {
        return $this->render("/article/show.html.twig",[
            'categories'=>$this->articleService->show(),
        ]);
      }
       #[Route('/admin/article/edit/{id}', name:'article.edit' , methods:['GET'])]
       public function edit(Request $request)
       {
            return $this->render('/article/edit.html.twig',[
                'article'=>$this->articleService->edit($request->get('id')),
                'categories'=>$this->articleService->show(),
                'data'=>$request->get('data')
            ]);
       }
        #[Route('/admin/article/update', name:'article.update' , methods:['POST'])]
        public function update(Request $request)
        {
           $storage = $this->getParameter("kernel.project_dir") . "/public/img";
            $data=$this->articleService->update($request,$storage);
            return $this->redirectToRoute('article.edit',[
                'id'=>$request->get('articleId'),
                'data'=>$data,
            ]);
        }
        #[Route('/user/article/getArticle/{id}',name:'article.getArticle',methods:['GET'])]
        public function getArticle(Request $request)
        {
            //dd( $this->articleService->getArticle($request->get('id')));
            return $this->render('/article/getArticle.html.twig',[
                'article'=> $this->articleService->getArticle($request->get('id'))
            ]);
           
        }

}
