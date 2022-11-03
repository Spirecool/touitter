<?php 

namespace App\Form;

use App\Entity\Post;
use LengthException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File as FileFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
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
                    new Length (["min"=> 6, "max"=> 150, "minMessage" => "Le contenu doit faire au minimum 6 caractères", "maxMessage" => "Le contenu doit faire au maximum 150 caractères" ])
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
            // ->add("image", UrlType::class, [
            //     "label"=> "URL de l'image", 
            //     "required" => true,
            //     "constraints" => [new Url (["message" => "L'image doit avoir une URL valide "]) ]
            // ]);

            ->add("image", FileType::class, [
                "label"=> "L'image",
                "mapped" => false, 
                "required" => false,
                "constraints" => [
                    new File ([
                        'maxSize' => "2048k",
                        "mimeTypes" => [
                            "image/jpeg",
                            "image/gif",
                            "image/png",
                            "image/jpg",
                            "image/webp",
                            "image/svg+xml"
                        ],
                            'mimeTypesMessage' => 'Merci d\'uploader une image valide(.gif, .jpeg, .jpg, .png, .svg ou .webp)',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => Post::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'post_item',
        ]);
    }
}

