<?php
// src/Form/Category/CreateForm.php
namespace App\Form\Category;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class UpdateForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title',TextType::class,[
            'required' => true,
        ]);
        $builder->add('description',TextareaType::class,[
            'required' => false,
        ]);
        $builder->add('image', FileType::class,[
        'required' => false,
        ]);
        $builder->add('submit',SubmitType::class);
    }


}