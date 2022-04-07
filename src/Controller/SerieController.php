<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

/**
 * @Route ("/series", name="serie_")
 */
class SerieController extends AbstractController
{
    /**
     * @Route("/series", name="serie_list")
     */
    public function list(SerieRepository $serieRepository): Response
    {
        $series = $serieRepository->findBestSeries();


        return $this->render('serie/list.html.twig', [

            "series" => $series
        ]);
    }

    /**
     * @Route("/series/details/{id}", name="serie_details")
     */
    public function details($id, SerieRepository $serieRepository): Response
    {
        $serie = $serieRepository ->find($id);

        if (!$serie) {
            throw $this->createNotFoundException('oh no !!!');
        }
        return $this->render('serie/details.html.twig', [
            "serie" => $serie
        ]);
    }

    /**
     * @Route("/series/create", name="serie_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $serie = new Serie();
        $serie->setDateCreated(new \DateTime());
        $serieForm = $this->createForm(SerieType::class, $serie);
        $serieForm->handleRequest($request);
        if ($serieForm->isSubmitted() && $serieForm->isValid()) {
            $entityManager->persist($serie);
            $entityManager->flush();
            $this->addFlash('success', 'Serie added! Good job');
            return $this->redirectToRoute('serie_serie_details', ['id'=>$serie->getId()]);
        }
        return $this->render('serie/create.html.twig', [
            'serieForm' => $serieForm->createView()
        ]);
    }

    /**
     *  @Route("/delete/{id}", name="delete")
     */
    public function delete(Serie $serie, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($serie);
        $entityManager->flush();
        return $this->redirectToRoute('main_home');
    }

}
