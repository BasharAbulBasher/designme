<?php

namespace App\Controller;

use App\Form\Category\CreateForm;
use App\Form\Category\UpdateForm;
use App\Services\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryController extends AbstractController
{
    private CategoryService $categoryService; 
    public function __construct(EntityManagerInterface $entityManagerInterface, ValidatorInterface $validator)
    {
        $this->categoryService = new CategoryService($entityManagerInterface, $validator);
    }
    #[Route('/admin/category/create', name: 'category.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(CreateForm::class);
        $form->handleRequest($request);
        $data = $request->get('response');

        if ($form->isSubmitted()) {
            $storage = $this->getParameter("kernel.project_dir") . "/public/img";
            $response = $this->categoryService->create($form, $storage);
            return $this->redirectToRoute('category.create', [
                'response' => $response,
            ]);
        }
        return $this->render('category/create.html.twig', [
            'form_definition' => $form->createView(),
            'data' => $data,
        ]);
    }

    #[Route('/admin/category/show', name:'category.show')]
    public function show()
    {
        return $this->render('category/show.html.twig',[
            'categories'=>$this->categoryService->show()
        ]);
    }
    #[Route('/admin/category/edit/{id}', name:'category.edit', methods:['GET','POST'])]
    public function edit(Request $request)
    {
        $category=$this->categoryService->getCategory($request->get('id'));
        $form=$this->createForm(UpdateForm::class);
        $form->get('title')->setData($category->getTitle());
        $form->get('description')->setData($category->getDescription());
        $form->handleRequest($request);
        $response=$request->get('response');
        if ($form->isSubmitted()) {
            $storage = $this->getParameter("kernel.project_dir") . "/public/img";
            $response=$this->categoryService->update($form, $storage,$category);
            return $this->redirectToRoute('category.edit',[
                'id'=>$category->getId(),
                'response'=>$response,
            ]);
        }

        return $this->render('category/edit.html.twig',[
            'form_definition'=>$form,
            'category'=>$category,
            'data'=>$response
        ]);
    }

    #[Route('/user/category/getCategory/{id}',name:'category.getCategory', methods:['GET'])]
    public function getCategory(Request $request)
    {
        return $this->render('category/getCategory.html.twig',[
            'category'=>$this->categoryService->edit($request->get('id')),
        ]);
    }



}