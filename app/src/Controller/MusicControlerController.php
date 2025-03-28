<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MusicRepository;
use App\Form\MusicType;
use App\Entity\Music;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface; // ✅ You forgot to import this!

final class MusicControlerController extends AbstractController
{
    #[Route('/', name: 'app_music_controler')]
    public function index(
        MusicRepository $musicRepo
    ): Response {
        $musics = $musicRepo->findAll();
        return $this->render('music_controler/index.html.twig', [
            'musicLists' => $musics,
        ]);
    }

    #[Route('/music/new', name: "app_music_new")]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager // ✅ Fixed typo here
    ): Response {
        $music = new Music();
        $form = $this->createForm(MusicType::class, $music); // ✅ Use $music, not new Music()

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $music = $form->getData();

            $entityManager->persist($music); // ✅ Fixed "presist" → "persist"
            $entityManager->flush();

            return $this->redirectToRoute('app_music_controler'); // ✅ Make sure this route exists
        }

        return $this->render('music_controler/new.html.twig', [
            'form' => $form->createView(), // ✅ You need to call createView() for the form
        ]);
    }
}
