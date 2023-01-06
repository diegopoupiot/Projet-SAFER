<?php

namespace App\Controller;

use App\Repository\BienRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BienController extends AbstractController
{
    #[Route('/bien', name: 'app_bien')]
    public function index(BienRepository $bienRepository, CategorieRepository $categorieRepository): Response
    {

        if (!empty($_POST['Price'])) {
            $price = $_POST['Price'];
        } else {
            $price = 999999;
        }


        if (isset($_POST["Categ"]) && isset($_POST["Type"]) && isset($_POST["local"])) {

            $id = $_POST["Categ"];
            $type = $_POST["Type"];
            $local = $_POST["local"];


            $intitCateg = $categorieRepository->affichageIntit($id);
            $intitType = ($type == 0) ? 'Achat' : 'Location';


            if ($id == "") {
                if ($type == "") {
                    if ($local == "") {
                        $lesBiens = $bienRepository->findAll();
                        $titre = 'Affichage des biens';
                    } else {
                        $lesBiens = $bienRepository->findBy(['cp' => $local]);
                        $titre = "Affichage Localisation : " . $local;
                    }
                } else {
                    if ($local == "") {
                        $lesBiens = $bienRepository->findBy(['is_locatif' => $type]);
                        $titre = 'Affichage ' . $intitType;
                    } else {
                        $lesBiens = $bienRepository->findBy(['is_locatif' => $type, 'cp' => $local]);
                        $titre = "Affichage " . $intitType . " à : " . $local;
                    }
                }
            } elseif ($type == "") {
                if ($local == "") {
                    $lesBiens = $bienRepository->findBy(['id_categorie' => $id, 'is_locatif' => $type]);
                    $titre = "Affichage Catégorie : " . $intitCateg[0]["intitule"];
                } else {
                    $lesBiens = $bienRepository->findBy(['id_categorie' => $id, 'is_locatif' => $type, 'cp' => $local]);
                    $titre = "Affichage : " . $intitCateg[0]["intitule"] . " à : " . $local;
                }
            } else {
                if ($local == "") {
                    $lesBiens = $bienRepository->findBy(['id_categorie' => $id, 'is_locatif' => $type]);
                    $titre = "Affichage : " . $intitCateg[0]["intitule"] . " en " . $intitType;
                } else {
                    $lesBiens = $bienRepository->findBy(['id_categorie' => $id, 'is_locatif' => $type, 'cp' => $local]);
                    $titre = "Affichage Catégorie : " . $intitCateg[0]["intitule"] . " et " . $intitType . " à : " . $local;
                }

            }
        } else {
            $lesBiens = $bienRepository->findAll();
            $titre = 'Affichage des biens';
        }

        return $this->render('bien/index.html.twig', [
            'lesBiens' => $lesBiens,
            'lesCateg' => $categorieRepository->findAll(),
            'localisation' => $bienRepository->getLocalisation(),
            'titre' => $titre,
            'prix' => $price,
        ]);
    }

    #[Route('/bien/{id}', name: 'app_bien_show')]
    public function show(CategorieRepository $categorieRepository, BienRepository $bienRepository, int $id): Response
    {
        return $this->render('bien/show.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'bien' => $bienRepository->find($id),
        ]);
    }
}
