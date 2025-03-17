<?php 
namespace App\Services;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Image;
use App\Helper\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ArticleService
{
    private EntityManagerInterface $entityManegerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManegerInterface=$entityManagerInterface;
    }
    public function save(Request $request, $storage)
    {
        //Intilize Article
        $article=new Article();
        //Sett Articles Attrs
        $article->setTitle($request->get('title'));
        $article->setDescription($request->get('description'));
        //Find the Category
        $category=$this->entityManegerInterface->getRepository(Category::class)->find($request->get('category_id'));
        $article->setCategoryId($category);
        //Implement SQL-Statment
        $this->entityManegerInterface->persist($article);
        //Execute the SQL-Statment
        $this->entityManegerInterface->flush();
        //Save the Images of Article
        if(!empty($request->files->get('images')))
        {
            $images=$request->files->get('images');
            $path="article/".$article->getId();
            foreach($images as $image)
            {
                $fileName=$this->saveImageInPublic($image, $storage, $path);
                //save the image in DB
                $image_entity=new Image();
                //Set Image Attrs
                $image_entity->setPath($fileName);
                $image_entity->setArticleId($article);
                //Impelembt the SQL-Statment
                $this->entityManegerInterface->persist($image_entity);
                //Execute the SQL-Statment
                $this->entityManegerInterface->flush();
            }
        }
        return [
            "success"=>true,
            "message"=>"Successfully created Article"
        ];
    }
    public function show()
    {
        $categories=$this->entityManegerInterface->getRepository(Category::class)->findAll();
        $category_arr=[];
        foreach($categories as $category){
            array_push($category_arr, $category->toArray());
        }
        return $category_arr;
    }
    public function edit($id)
    {
        $article=$this->entityManegerInterface->getRepository(Article::class)->find($id);
        return $article->toArray();
    }
    public function update(Request $request,$storage)
    {
        //find the Article
        $article=$this->entityManegerInterface->getRepository(Article::class)->find($request->get("articleId"));
        //Set Articles Attrs
        $article->setTitle($request->get('title'));
        $article->setDescription($request->get('description'));
        //find the category
        $category=$this->entityManegerInterface->getRepository(Category::class)->find($request->get('categoryId'));
        //set the Category
        $article->setCategoryId($category);
        //Update the Article
        $this->entityManegerInterface->persist($article);
        $this->entityManegerInterface->flush();
        //Save images if exist
        if(!empty($request->files->get('images')))
        {
            //Generate the path of the Image
            $path="article/".$article->getId();
            foreach($request->files->get('images') as $image)
            {
                //Save the Image in Public and get uniqe File-name
                $fileName=$this->saveImageInPublic($image,$storage,$path);
                //Inject. an Image
                $image=new Image();
                //Set the Images Attrs
                $image->setArticleId($article);
                $image->setPath($fileName);
                //Save the Image in DB
                $this->entityManegerInterface->persist($image);
                $this->entityManegerInterface->flush();
            }
        }
        //Drop Images if exist
        if(!empty($request->get('imagesToDelete')))
        {
            $file=new File();
            $path="article/".$article->getId();
            foreach($request->get('imagesToDelete') as $imageId)
            {
                //Find the Image to delete it
                $imageToDelete=$this->entityManegerInterface->getRepository(Image::class)->find($imageId);
                //Remove the Image from Public
                $fileName=$imageToDelete->getPath();
                $file->removeFile ($storage, $path, $fileName);
                //Delete the Image from DB
                $this->entityManegerInterface->remove($imageToDelete);
                $this->entityManegerInterface->flush();
            }
        } 
        return[
            'success'=>true,
            'message'=>'Successfully updated '
        ];

    }
    public function getArticle($id)
    {
        $article=$this->entityManegerInterface->getRepository(Article::class)->find($id);
        return $article->toArray();
    }
    //Private Functions---------------------------------
    private function saveImageInPublic(UploadedFile $image,$storage,$path)
    {
        $file=new File();
        $file->setFile($image);
        return $file->saveFile($storage,$path);

    }
}