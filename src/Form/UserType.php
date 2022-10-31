<?php 

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use LengthException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;

class UserType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder    
            ->add("username", TextType::class, [
                "label" => "Nom d'utilisateur", 
                "required" => true,
                "constraints" => [
                    new Length (["min"=> 6, "max"=> 150, "minMessage" => "Le nom d'utilisateur doit faire au minimum 6 caractères", "maxMessage" => "Le nom d'utilisateur doit faire au maximum 150 caractères" ])
                ]
            ])
            ->add("password", PasswordType::class, [
                "label"=> "Mot de passe", 
                "required" => true,
                "constraints" => [
                    new Length (["min"=> 5, "max"=> 320, "minMessage" => "Le mot de passe doit faire au minimum 5 caractères", "maxMessage" => "Le mot de passe doit faire au maximum 320 caractères" ]),
                    new NotBlank(["message" => "Le mot de passe ne doit pas être vide !"])
                ]
            ])
            ->add("confirm", PasswordType::class, [
                "label"=> "Confirmer le mot de passe", 
                "required" => true,
                "constraints" => [
                    new NotBlank(["message" => "Le mot de passe ne doit pas être vide"]),
                    //fonction de callback qui vérifie que les 2 mots de passe sont bien identiques 
                    new Callback(['callback' => function($value, ExecutionContext $ec) {
                        if ($ec->getRoot()['password']->getViewData() !== $value) {
                            $ec->addViolation("Les mots de passe ne sont pas identiques");
                        }
                    }])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'post_item',
        ]);
    }
}

