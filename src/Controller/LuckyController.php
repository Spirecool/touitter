<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
// on importe le composant pour définir la route de notre contrôleur 
// à l'aide d'une annotation
use Symfony\Component\Routing\Annotation\Route;

class LuckyController extends AbstractController
{
    /**
     * @Route("/lucky/number", name="app_lucky_number")
     */

    public function number(): Response
    {
        $number = random_int(0, 100);
        return $this->render('/lucky/number.html.twig', [
            'number' => $number
        ]);
    }
}


