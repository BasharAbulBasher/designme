<?php 
namespace App\Services;

use App\Entity\Category;
use App\Entity\Image;
use App\Helper\File;
use App\Helper\Validation;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryService
{
    private EntityManagerInterface $entityManagerInterface;
    private ValidatorInterface $validator;
    private CategoryRepository $categoryRepo;
    public function __construct(EntityManagerInterface $entityManagerInterface, ValidatorInterface $validator)
    {
        $this->entityManagerInterface=$entityManagerInterface;
        $this->validator=$validator;
        $this->categoryRepo=$this->entityManagerInterface->getRepository(Category::class);
    }
    public function create($form, string $storage)
    {
        //create new Category
        $category=new Category(); 
        //set the Attributes of the Category
        $category->setTitle($form->get('title')->getData());
        $category->setDescription($form->get('description')->getData());
        //tell Doctrine you want to save the Category "no quereies yet"
        $this->entityManagerInterface->persist($category);
        //Execute the queries
        $this->entityManagerInterface->flush();
        //save Image
        $dir_path="category/".$category->getId();
        $file_name=$this->saveImage($storage,$dir_path,$form->get('image')->getData());
        $this->createImage($file_name,$category);
    
        return [
            'success'=>true,
            'message'=>' Successfully Category saved'
        ];
    }
    public function show()
    {
        $categories_arr=[];
        //convert Collection to Array 
        foreach($this->categoryRepo->findAll() as $category)
        {
            array_push($categories_arr,$category->toArray());
        }
       return $categories_arr;
    }
    public function edit($id)
    {
       return $this->categoryRepo->find($id)->toArray();
    }

    public function getCategory($id)
    {
        return $this->categoryRepo->find($id);
    }

    public function update($form, $storage,Category $category)
    {
        $categoryInArray=$category->toArray();
        //dd($categoryInArray['images'][0]['id']);
        //Set Attrs of the Category
        $category->setTitle($form->get('title')->getData());
        $category->setDescription($form->get('description')->getData());

        //Update the Image
        if(!empty($form->get('image')->getData()))
        {
            //get Directory Path '/category/id'
            $dir_path="category/".$category->getId();
            //get Image Id
            $imageId=$categoryInArray['images'][0]['id'];
            //get Image Name
            $file_name=$categoryInArray['images'][0]['path'];
            //remove the old Image from Public Directory
            $this->removeImage($storage, $dir_path, $file_name);
            //delete the old Image from DB Table Image
            $this->deleteImage($imageId);
            //create the new Image
            $image=new Image();
            $file_name=$this->saveImage($storage,$dir_path,$form->get('image')->getData());
            $image->setPath($file_name);
            $image->setCategoryId($category);
            $this->entityManagerInterface->persist($image);
        }
        //Update the Category
        $this->entityManagerInterface->flush();
        return[
            'success'=>true,
            'message'=>'Category updated Successfully'
        ];
    }



    //Private Functions----------------------------------------------
    /**
     * Save the Image in Public directory 'public/img/...'
     */
    private function saveImage(string $storage, string $dir_path, UploadedFile $img)
    {
        $file=new File();
        $file->setFile($img);
        return $file->saveFile($storage, $dir_path);
    }
    /**
     * Create an Image in DB 'Table Image'
     */
    private function createImage($fileName,Category $category)
    {
        //set image Attrs
        $img=new Image();
        $img->setPath($fileName);
        $img->setCategoryId($category);
        //save Image in DB
        $this->entityManagerInterface->persist($img);
        $this->entityManagerInterface->flush();
    }
    /**
     * Remove the Image from public Directory 'public/img/...
     */
    private function removeImage(string $storage, string $dir_path, string $file_name)
    {
        $file=new File();
        $file->removeFile($storage,$dir_path,$file_name);

    }
    /**
     * Delete an Image from DB Table Image
     */
    private function deleteImage($imageId)
    {
        //Find the Imafe using imageRepository
        $image=$this->entityManagerInterface->getRepository(Image::class)->find($imageId);
        $this->entityManagerInterface->remove($image);
        $this->entityManagerInterface->flush();
    }
    
}