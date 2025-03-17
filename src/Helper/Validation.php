<?php
namespace App\Helper;
class Validation
{
    private  $validationErrors=[];
    public function __construct($validationErrors)
    {
        $this->validationErrors=$validationErrors;

    }
    public function isValade()
    {
        if(count($this->validationErrors)>0)
        {
            return true;
        }
        return false;
    }
    public function getValadationErrors()
    {
        $message=[];
        if($this->isValade())
        {
            foreach($this->validationErrors as $error)
            {
                $content=[];
                $content['attr']=$error->getPropertyPath();
                $content['message']=$error->getMessageTemplate();
                array_push($content,$message);
            }
        }
        return $message;
    }
}