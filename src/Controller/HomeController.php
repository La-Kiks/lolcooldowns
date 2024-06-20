<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Logic\ObjectChampions;


class HomeController extends AbstractController
{
    #[Route("/", name: "home")]
    function index(Request $request, LoggerInterface $logger, string $publicDir ): Response
    {
        $championsObject = new ObjectChampions($logger, $publicDir);
        $data = $championsObject->getChampionsData();
        //dd($data);
        $names = ['Name', 'Name', 'Name', 'Name', 'Name'];
        //$champions = [];

        if ($request->isMethod('POST')) {
            $inputName1 = $request->request->get('name1');
            $inputName2 = $request->request->get('name2');
            $inputName3 = $request->request->get('name3');
            $inputName4 = $request->request->get('name4');
            $inputName5 = $request->request->get('name5');
            $names = array($inputName1, $inputName2, $inputName3, $inputName4, $inputName5);
        }
        foreach ($names as $name){
            $dataChampion = array_filter($data, function ($champion) use ($name){
                return $champion['name'] === $name;
            });
            $champions[] = $dataChampion;
        }

        return $this->render('home/index.html.twig',
        [
            'names' => $names,
            'champions_list' => $champions,
        ]);
    }
}
