<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Publication;
use App\Form\Publication1Type;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/publication")
 */
class PublicationController extends AbstractController
{
    /**
     * @Route("/", name="app_publication_index", methods={"GET"})
     */
    public function index(PublicationRepository $publicationRepository): Response
    {
        $filters = ['test', 'yellow', 'green'];
        $publications = $publicationRepository->findAll();
        foreach ($publications as $q){
            $newContent = str_replace($filters, "******", $q->getDescription());
            $q->setDescription($newContent);
        }
        return $this->render('publication/index.html.twig', [
            'publications' => $publicationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_publication_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PublicationRepository $publicationRepository , EntityManagerInterface $entityManager): Response
    {
        $publication = new Publication();
        if ($request->isMethod('post')) {
            $brochureFile = $request->files->get('image');
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $publication->setDescription($request->get('description'));
                $publication->setNom($request->get('nom'));
                $publication->setDate(new \DateTimeImmutable('now'));
                $publication->setImage($newFilename);
                $entityManager->persist($publication);
                $entityManager->flush();
                return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);

            }}

        return $this->render('publication/new.html.twig', [


        ]);
    }

    /**
     * @Route("/search_comment", name="commSearch", methods={"POST"})
     */
    public function commentsearch(Request $request, PublicationRepository $questionsRepository)
    {
        $questions = $request->get('data');
        $quest = $questionsRepository->findQuestionDQL($questions);
        var_dump($quest);

    }

    /**
     * @Route("/publication_show/{id}", name="questshow", methods={"GET","POST"})
     */
    public function showw(Request $request): Response
    {
        $filters = ['test', 'yellow', 'fazfazdazcc'];
        $publication = $this->getDoctrine()->getRepository(Publication::class)->find($request->get('id')+0);
        //$session = $this->getDoctrine()->getRepository(User::class)->find(1);
        //$number = $this->getDoctrine()->getRepository(Questions::class)->findReponsesNumberDQL($request->get('id')+0);
        $commentaires = $this->getDoctrine()->getRepository(Commentaire::class)->findBy(["pub" =>$request->get('id')+0]);
        foreach ($commentaires as $q){
            $newContent = str_replace($filters, "******", $q->getTextCommentaire());
            $q->setTextCommentaire($newContent);
        }

        return $this->render('publication/commentaires.html.twig',[
            'publication' => $publication,
            'commentaires' => $commentaires,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_publication_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Publication $publication, PublicationRepository $publicationRepository): Response
    {
        $form = $this->createForm(Publication1Type::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publicationRepository->add($publication);
            return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publication/edit.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_publication_delete", methods={"POST","DELETE"})
     */
    public function delete(Request $request, Publication $publication, PublicationRepository $publicationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publication->getId(), $request->request->get('_token'))) {
            $publicationRepository->remove($publication);
        }

        return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);
    }
}
