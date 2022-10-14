<?php 

namespace App\Form;

use App\Entity\Post;
use LengthException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder    
            ->add("title", TextType::class, [
                "label" => "Titre", 
                "required" => true,
                "constraints" => [
                    new Length (["min"=> 5, "max"=> 320, "minMessage" => "Le contenu doit faire au minimum 5 caractères", "maxMessage" => "Le contenu doit faire au maximum 320 caractères" ])
                ]
            ])
            ->add("content", TextareaType::class, [
                "label"=> "Contenu", 
                "required" => false,
                "constraints" => [
                    new Length (["min"=> 5, "max"=> 320, "minMessage" => "Le contenu doit faire au minimum 5 caractères", "maxMessage" => "Le contenu doit faire au maximum 320 caractères" ]),
                    new NotBlank(["message" => "Le contenu ne doit pas être vide"])
                ]
            ])
            ->add("image", UrlType::class, [
                "label"=> "URL de l'image", 
                "required" => true,
                "constraints" => [new Url (['message' => "L'image doit avoir une URL valide "]) ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Post::class,
        ]);
    }
}

