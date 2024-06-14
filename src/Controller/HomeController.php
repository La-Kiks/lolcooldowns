<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Logic\Champions;


class HomeController extends AbstractController
{
    #[Route("/", name: "home")]
    function index(Request $request, LoggerInterface $logger, string $publicDir ): Response
    {
        $champions = new Champions($logger, $publicDir);
        $data = $champions->getChampionsData();
        $names = ['Alice', 'Bob', 'Charlie', 'David'];
        $message = null;

        if ($request->isMethod('POST')) {
            $inputName = $request->request->get('name');
            if (in_array($inputName, $names)) {
                $message = "$inputName is in the list.";
            } else {
                $message = "$inputName is not in the list.";
            }
        }
        var_dump($message);
        return $this->render('home/index.html.twig',
        [
            'names' => $names,
            'message' => $message
        ]);
    }
}
