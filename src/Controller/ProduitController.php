<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/produit", name="produit_")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function list(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findBy(['published' => true] , ['date_add' => 'DESC']);
        return $this->render('produit/index.html.twig', [
            'produits' => $produits
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager):Response {
        $produit = new Produit();
        $produit->setDateAdd(new \DateTime());
        $produit->setDateEdit(new \DateTime());

        $produitForm = $this->createForm(ProduitType::class, $produit);
        $produitForm->handleRequest($request);

        if ($produitForm->isSubmitted() && $produitForm->isValid() ){
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Produit successfully added!');
            return $this->redirectToRoute('produit_list');
            //return $this->redirectToRoute('produit_detail', ['id' => $produit->getId()]);
        }

        return $this->render('produit/create.html.twig', [
            'produitForm' => $produitForm->createView()
        ]);

    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function detail(int $id, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);

        // s'il n'existe pas en bdd, on déclenche une erreur 404
        if (!$produit){
            throw $this->createNotFoundException('This produits does not exists! Sorry!');
        }

        return $this->render('produit/detail.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/details/update/{id}", name="update")
     */
    public function update(Request $request, int $id, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {
        $produit = $produitRepository->find($id);
        // s'il n'existe pas en bdd, on déclenche une erreur 404
        if (!$produit){
            throw $this->createNotFoundException('This produits does not exists! Sorry!');
        }

        $produit->setDateEdit(new \DateTime());

        $produitForm = $this->createForm(ProduitType::class, $produit);
        $produitForm->handleRequest($request);

        if ($produitForm->isSubmitted() && $produitForm->isValid() ){
            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Produit successfully updated!');
            //return $this->redirectToRoute('produit_list');
            return $this->redirectToRoute('produit_details', ['id' => $produit->getId()]);
        }

        return $this->render('produit/update.html.twig', [
            'produitForm' => $produitForm->createView(),
        ]);
    }
    /**
     * @Route("/details/delete/{id}", name="delete")
     */
    public function delete(Request $request, int $id, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {
        $produit = $produitRepository->find($id);
        // s'il n'existe pas en bdd, on déclenche une erreur 404
        if (!$produit){
            throw $this->createNotFoundException('This produits does not exists! Sorry!');
        }

        $produitRepository->remove($produit,true);
        $this->addFlash('success', 'Produit successfully removed!');
        return $this->redirectToRoute('produit_list');
    }
}
