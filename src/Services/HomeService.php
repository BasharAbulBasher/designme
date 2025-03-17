<?php 
namespace App\Services;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class HomeService
{
    private EntityManagerInterface $entityManagerInterface;
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface=$entityManagerInterface;
    }
    public function getCategories()
    {
        $categories=$this->entityManagerInterface->getRepository(Category::class)->findAll();
        $categories_arr=[];
        foreach($categories as $category)
        {
            array_push($categories_arr, $category->toArray());
        }
        return $categories_arr;
    }
}