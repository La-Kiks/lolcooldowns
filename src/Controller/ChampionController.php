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
    #[Route('/', name: 'app.champion', methods: ['GET', 'POST'])]
    public function index(ChampionRepository $championRepository,
                            Request $request,
    PaginatorInterface $paginator): Response
    {
        // Load the full table & paginate it : (not useful imo)
//        $data = $championRepository->findAll();
//        $champions = $paginator->paginate($data, $request->query->getInt('page', 1), 10);

        $searchData = new SearchData();
        $champions = null;

        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        $cooldownMultipliers = array_fill(0, 10, 1);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $champions = $championRepository->findBySearch($searchData);

        }

        return $this->render('home/champions.html.twig', [
            'form' => $form,
            'champions' => $champions,
            'cooldown_multipliers' => $cooldownMultipliers,
        ]);
    }

    /**
     * @param int $haste is the value of haste.
     * @return float cooldown reduction multiplier.
     *
     * For exemple with 100 haste : 100 / (100 + 100) = 0.5
     */
    private function cooldownReduction(int $haste): float
    {
        // reduced cooldown = base cooldown x 100 / (100 + haste)
        // cooldown reduction = (haste / (haste + 100)) x 100

        return ($haste / ($haste + 100));
    }
}