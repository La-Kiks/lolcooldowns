<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\ChampionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChampionController extends AbstractController
{
    #[Route('/champion', name: 'app.champion', methods: ['GET', 'POST'])]
    public function index(ChampionRepository $championRepository,
                            Request $request,
    PaginatorInterface $paginator): Response
    {
        $data = $championRepository->findAll();

        $champions = $paginator->paginate($data, $request->query->getInt('page', 1), 10);
        $searchData = new SearchData();

        // dd($champions[0]->getSpells()[0]->getCooldowns());

        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        dd($request);

//        dd($searchData->haste);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $champions = $championRepository->findBySearch($searchData);

            if($searchData->haste !== 0){
                $searchData->multiplier = $this->cooldownReduction($searchData->haste);
            }

        }

        return $this->render('home/champions.html.twig', [
            'form' => $form->createView(),
            'champions' => $champions,
            'cooldown_multiplier' => $searchData->multiplier,
        ]);
    }

    private function cooldownReduction(int $haste): float
    {
        // reduced cooldown = base cooldown x 100 / (100 + haste)
        // cooldown reduction = (haste / (haste + 100)) x 100

        return ($haste / ($haste + 100));
    }
}