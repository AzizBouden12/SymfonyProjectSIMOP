<?php

namespace App\Controller;

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class LoginController extends AbstractController
{
    #[Route('/home', name: 'Home')]
    public function index(): Response
    {
        return $this->render('Home/BL.html.twig');

    }


    #[Route('/mot-de-passe-oublie', name: 'forgottenPassword')]
    public function forgottenPassword(): Response
    {

        return $this->render('login\ForgotPassword.html.twig');

    }

    #[Route('/liste-commandes', name: 'ListeCommandes')]
    public function ListeCommandes(): Response
    {

        return $this->render('Home\Home.html.twig');

    }

    #[Route('/commande', name: 'Commande')]
    public function Commande(): Response
    {

        return $this->render('Home\Commande.html.twig');

    }

    #[Route('/liste-factures', name: 'ListeFactures')]
    public function ListeFactures(): Response
    {

        return $this->render('Home\ListeFactures.html.twig');

    }

    #[Route('/facture', name: 'Facture')]
    public function Facture(): Response
    {

        return $this->render('Home\Facture.html.twig');

    }

    #[Route('/BL', name: 'BL', methods: ['POST'])]
    public function BL(Request $request): Response
    {
        $blDetails = [];
        $bls = [];
        $listofBLS = [];
        $nbrlbls = 0;
        $fournisseur = $request->request->get('fournisseur');
        $numFacture = $request->request->get('numFacture');
        $dateFactureS = $request->request->get('dateFacture');
        $totalFacture = $request->request->get('totalFacture');
        $checkedValues = $request->request->get('checkedValues');
        $checkedValuesArray = json_decode($checkedValues, true);

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = new \SoapClient("https://srv-webserver.domgp.local/TestService/Service1.asmx?WSDL");
        $listeFournisseur = $client->__soapCall('listeFournisseurs', array())->listeFournisseursResult->string;

        if (!empty($listeFournisseur)) {
            $ntb = count($listeFournisseur);
        } else {
            $ntb = 0;
        }
        $incr = $ntb / 2;

        if (isset($_POST['verifyBLs'])) {
            $params = array('Nfacture' => $numFacture, 'cf' => $fournisseur, 'dateFacture' => $dateFactureS);
            $listeBLs = $client->__soapCall('ListeBLS', array($params));

            $bls = [];
            $blDetails = [];

            if (!empty($listeBLs->ListeBLSResult->string)) {
                $listofBLS = $listeBLs->ListeBLSResult->string;
                $nbrlbls = count($listofBLS);

                for ($i = 0; $i < $nbrlbls; $i = +10) {
                    $montant = $listofBLS[$i];
                    $cf = $listofBLS[$i + 1];
                    $dateCommande = $listofBLS[$i + 2];
                    $numCommande = $listofBLS[$i + 3];
                    $quantite = $listofBLS[$i + 4];
                    $designation = $listofBLS[$i + 5];
                    $reference = $listofBLS[$i + 6];

                    $bls[] = [
                        'numBL' => $numCommande,
                        'montant' => $montant
                    ];


                    $blDetails[] = [
                        'numCommande' => $numCommande,
                        'reference' => $reference,
                        'designation' => $designation,
                        'montant' => $montant,
                        'quantite' => $quantite,
                    ];
                }
            }
        }
        dd($nbrlbls);
        dd($listofBLS);
        $nbrlbls=$nbrlbls/10;
        return $this->render('Home/BL.html.twig', [
            'listefournisseur' => $listeFournisseur,
            'listeBLs' => $listofBLS,
            'incr' => $incr,
            'nbrlbls' => $nbrlbls,
            'bls' => $bls,
            'blDetails' => $blDetails,
        ]);
    }

}




