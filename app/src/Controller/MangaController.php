<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Form\MangaType;
use App\Form\MangaSearchType;
use App\Repository\MangaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/')]
#[IsGranted('ROLE_USER')]
final class MangaController extends AbstractController
{
    #[Route(name: 'app_manga_index', methods: ['GET'])]
    public function index(Request $request, MangaRepository $mangaRepository): Response
    {
        $searchForm = $this->createForm(MangaSearchType::class);
        $searchForm->handleRequest($request);
        
        $user = $this->getUser();
        $mangas = [];
        
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            // Si un formulaire de recherche est soumis, utiliser les critères pour filtrer
            $mangas = $mangaRepository->findBySearch($request->query->all(), $user);
        } else {
            // Sinon, afficher tous les mangas de l'utilisateur
            $mangas = $mangaRepository->findBy(['owner' => $user]);
        }
        
        return $this->render('manga/index.html.twig', [
            'mangas' => $mangas,
            'searchForm' => $searchForm->createView(),
        ]);
    }

    #[Route('/manga/new', name: 'app_manga_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $manga = new Manga();
        // Définir l'utilisateur connecté comme propriétaire
        $manga->setOwner($this->getUser());
        
        $form = $this->createForm(MangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($manga);
            $entityManager->flush();

            return $this->redirectToRoute('app_manga_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('manga/new.html.twig', [
            'manga' => $manga,
            'form' => $form,
        ]);
    }

    #[Route('/manga/{id}', name: 'app_manga_show', methods: ['GET'])]
    public function show(Manga $manga): Response
    {
        // Vérifier que l'utilisateur est le propriétaire
        $this->denyAccessUnlessGranted('view', $manga);
        
        return $this->render('manga/show.html.twig', [
            'manga' => $manga,
        ]);
    }

    #[Route('/manga/{id}/edit', name: 'app_manga_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Manga $manga, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est le propriétaire
        $this->denyAccessUnlessGranted('edit', $manga);
        
        $form = $this->createForm(MangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_manga_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('manga/edit.html.twig', [
            'manga' => $manga,
            'form' => $form,
        ]);
    }

    #[Route('/manga/{id}', name: 'app_manga_delete', methods: ['POST'])]
    public function delete(Request $request, Manga $manga, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est le propriétaire
        $this->denyAccessUnlessGranted('delete', $manga);
        
        if ($this->isCsrfTokenValid('delete'.$manga->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($manga);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_manga_index', [], Response::HTTP_SEE_OTHER);
    }
}