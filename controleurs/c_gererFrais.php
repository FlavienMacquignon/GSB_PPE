<?php
/**
 * Gestion des frais
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @author Flavien Macquignon <flavien.macquignon@fastmail.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

$idUser = $_SESSION['idUser'];
$mois = getMois(date('d/m/Y'));
$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
switch ($action) {
    case 'saisirFrais':
        if ($pdo->estPremierFraisMois($idUser, $mois)) {
            $pdo->creeNouvellesLignesFrais($idUser, $mois);
        }
        break;

    case 'validerMajFraisForfait':
        $lesFrais = filter_input(INPUT_POST, 'lesFrais', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (lesQteFraisValides($lesFrais)) {
            $pdo->majFraisForfait($idUser, $mois, $lesFrais);
        } else {
            ajouterErreur('Les valeurs des frais doivent être numériques');
            include 'vues/v_erreurs.php';
        }
        break;
    case 'validerCreationFrais':
        $dateFrais = filter_input(INPUT_POST, 'dateFrais', FILTER_SANITIZE_STRING);
        $libelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_STRING);
        $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
        valideInfosFrais($dateFrais, $libelle, $montant);
        if (nbErreurs() != 0) {
            include 'vues/v_erreurs.php';
        } else {
            $pdo->creeNouveauFraisHorsForfait(
                $idUser,
                $mois,
                $libelle,
                $dateFrais,
                $montant
            );
        }
        break;
    case 'supprimerFrais':
        $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
        if ($_SESSION['role'] == 1) {
            $pdo->supprimerFraisHorsForfait($idFrais);
        } else {
            $leFraisHorsForfait = $pdo->getLeFraisHorsForfait($idFrais);
            $leFraisHorsForfait['libelle'] = 'REFUSE ' . $leFraisHorsForfait['libelle'];
            if (strlen($leFraisHorsForfait['libelle']) > 100) {
                $leFraisHorsForfait['libelle'] = $leFraisHorsForfait['libelle'] . substr(0, 0, 100);
            }
            $leFraisHorsForfait['mois'] = $leFraisHorsForfait['mois'] + 1;
            if (substr($leFraisHorsForfait['mois'], -2) > 12) {
                $numAnnee = substr($leFraisHorsForfait['mois'], 0, -2) + 1;
                $leFraisHorsForfait = $numAnnee . '01';
            }
            $pdo->creeNouvellesLignesFrais($leFraisHorsForfait['visiteur'], $leFraisHorsForfait['mois']);
            $pdo->creeNouveauFraisHorsForfait(
                $leFraisHorsForfait['visiteur'],
                $leFraisHorsForfait['mois'],
                $leFraisHorsForfait['libelle'],
                dateAnglaisVersFrancais($leFraisHorsForfait['date']),
                $leFraisHorsForfait['montant']
            );
            $pdo->supprimerFraisHorsForfait($idFrais);
        }
        break;

    case 'choixFrais':
        $lesVisiteurs = $pdo->getTousLesVisiteurs();
        $lesMois[] = $pdo->getTousLesMoisDisponibles();
        $lesCles = array_keys($lesMois);
        $moisASelectionner = $lesCles[0];
        include 'vues/v_Comptables/v_validerFrais_c.php';
        break;

    case 'validerFrais':

        /*
         * DropDown
         */

        $lesVisiteurs = $pdo->getTousLesVisiteurs();
        $lesMois[] = $pdo->getTousLesMoisDisponibles();
        $lesCles = array_keys($lesMois);
        $moisASelectionner = $lesCles[0];
        include 'vues/v_Comptables/v_validerFrais_c.php';

        /*
         * Récupération du formulaire
         */
        $leVisiteur = filter_input(INPUT_POST, "lstVisiteur", FILTER_SANITIZE_STRING);
        $leMois = filter_input(INPUT_POST, "lstMoisVisiteur", FILTER_SANITIZE_STRING);
        $_SESSION['leVisiteur'] = $leVisiteur;
        $_SESSION['leMois'] = $leMois;
        if (is_null($leVisiteur) || is_null($leMois)) {
            if (is_null($leVisiteur)) {
                ajouterErreur('Un problème est survenu lors de la sélection du visiteur');
            }
            if (is_null($leMois)) {
                ajouterErreur('Un problème est survenu dans la sélection du mois');
            }
        }

        /*
         * Affichage des éléments de Frais
         */
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteur, $leMois);
        if ($lesFraisForfait[0] != null) {
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($leVisiteur, $leMois);
            $numAnnee = substr($leMois, 0, 4);
            $numMois = substr($leMois, 4, 2);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $montantValide = $lesInfosFicheFrais['montantValide'];
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
            include 'vues/v_Comptables/v_ficheFrais_c.php';
            break;
        } else {
            ajouterErreur('Il n\'y a pas de fiche de Frais pour ce visiteur ce mois');
            include "vues/v_erreurs.php";
            break;
        }


    case "soumettreFrais":
        $leVisiteur = $_SESSION['leVisiteur'];
        $leMois = $_SESSION['leMois'];
        unset($_SESSION['leMois']);
        unset($_SESSION['leVisiteur']);
        $lesFraisForfait = filter_input(INPUT_POST, "lesFraisForfait", FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        $pdo->majFraisForfait($leVisiteur, $mois, $lesFraisForfait);
        $lesFraisHF = $pdo->getLesFraisHorsForfait($leVisiteur, $leMois);
        $lesNouveauxFraisHF = array();
        $k = 0;
        foreach ($lesFraisHF as $unFraisHorsForfait) {
            $lesNouveauxFraisHF[$k] = array(
                'idLigneFraisHorsForfaitPK' => null,
                'idUserFK' => null,
                'mois' => null,
                'libelle' => null,
                'date' => null,
                'montant' => null
            );
            $idFraisHorsForfait = $unFraisHorsForfait['idLigneFraisHorsForfaitPK'];
            $lesNouveauxFraisHF[$k]['idLigneFraisHorsForfaitPK'] = $idFraisHorsForfait;
            $lesNouveauxFraisHF[$k]['idUserFK'] = $leVisiteur;
            $lesNouveauxFraisHF[$k]['mois'] = $leMois;
            $lesNouveauxFraisHF[$k]['libelle'] = filter_input(INPUT_POST, $idFraisHorsForfait . '$LIBELLE', FILTER_SANITIZE_STRING);
            $lesNouveauxFraisHF[$k]['date'] = filter_input(INPUT_POST, $idFraisHorsForfait . '$DATE', FILTER_SANITIZE_STRING);
            $lesNouveauxFraisHF[$k]['montant'] = filter_input(INPUT_POST, $idFraisHorsForfait . '$MONTANT', FILTER_VALIDATE_FLOAT);
            $k++;
        }
        $pdo->majFraisHF($lesNouveauxFraisHF);
        $nbJustificatifs = filter_input(INPUT_POST, 'nbJustificatifs', FILTER_SANITIZE_STRING);
        $pdo->majNbJustificatifs($leVisiteur, $leMois, $nbJustificatifs);
        break;

    case "reporterFrais":
        $idFrais = filter_input(INPUT_POST, 'btn_reporter', FILTER_SANITIZE_STRING);
        $leFraisHorsForfait = $pdo->getLeFraisHorsForfait($idFrais);
        if (strlen($leFraisHorsForfait['libelle']) > 100) {
            $leFraisHorsForfait['libelle'] = $leFraisHorsForfait['libelle'] . substr(0, 0, 100);
        }
        $leFraisHorsForfait['mois'] = $leFraisHorsForfait['mois'] + 1;
        if (substr($leFraisHorsForfait['mois'], -2) > 12) {
            $numAnnee = substr($leFraisHorsForfait['mois'], 0, -2) + 1;
            $leFraisHorsForfait = $numAnnee . '01';
        }
        $pdo->creeNouvellesLignesFrais($leFraisHorsForfait['visiteur'], $leFraisHorsForfait['mois']);
        $pdo->creeNouveauFraisHorsForfait(
            $leFraisHorsForfait['visiteur'],
            $leFraisHorsForfait['mois'],
            $leFraisHorsForfait['libelle'],
            dateAnglaisVersFrancais($leFraisHorsForfait['date']),
            $leFraisHorsForfait['montant']
        );
        $pdo->supprimerFraisHorsForfait($idFrais);
        break;
    case "suivreFrais":
        $lesVisiteurs = $pdo->getTousLesVisiteurs();
        $lesMois = $pdo->getTousLesMoisDisponibles();
        //FIXME vérifier cette fonction
        // $lesMois=supprimeDoublon($lesMois);
        $lesFichesFrais = array();
        $k = 0;
        foreach ($lesVisiteurs as $unVisiteur) {
            foreach ($lesMois as $unMois) {
                $infoFichesFrais = $pdo->getLesInfosFicheFrais($unVisiteur['id'], $unMois);
                if (isset($infoFichesFrais['idEtat'])) {
                    $lesFichesFrais[$k] = array(
                        'idVisiteur' => $unVisiteur['id'],
                        'nom' => $unVisiteur['nom'],
                        'prenom' => $unVisiteur['prenom'],
                        'mois' => $unMois,
                        'idEtat' => $infoFichesFrais['idEtat'],
                        'dateModif' => $infoFichesFrais['dateModif'],
                        'nbJustificatifs' => $infoFichesFrais['nbJustificatifs'],
                        'montantValide' => $infoFichesFrais['montantValide'],
                        'libEtat' => $infoFichesFrais['libEtat']);
                    $k++;
                }
            }
        }
        $_SESSION['lesFichesFrais'] = $lesFichesFrais;
        include 'vues/v_Comptables/v_suivitFrais_c.php';
        break;
    case 'recapFrais':
        $lesFichesFrais = $_SESSION['lesFichesFrais'];
        unset($_SESSION['lesFichesFrais']);
        $leVisiteurChoisi = filter_input(INPUT_POST, 'lstVisiteur', FILTER_SANITIZE_STRING);
        $leMoischoisi = filter_input(INPUT_POST, 'lstMois', FILTER_SANITIZE_STRING);
        $dateFr = dateAnglaisVersFrancais($lesFichesFrais[$leVisiteurChoisi]['dateModif']);

        $laFicheFrais = array(
            'idVisiteur' => $leVisiteurChoisi,
            'nom' => $lesFichesFrais[$leVisiteurChoisi]['nom'],
            'prenom' => $lesFichesFrais[$leVisiteurChoisi]['prenom'],
            'mois' => $leMoischoisi,
            'idEtat' => $lesFichesFrais[$leVisiteurChoisi]['idEtat'],
            'dateModif' => $dateFr,
            'nbJustificatifs' => $lesFichesFrais[$leVisiteurChoisi]['nbJustificatifs'],
            'montantValide' => $lesFichesFrais[$leVisiteurChoisi]['montantValide'],
            'libEtat' => $lesFichesFrais[$leVisiteurChoisi]['libEtat']
        );

        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($laFicheFrais['idVisiteur'], $laFicheFrais['mois']);
        $lesFraisForfait = $pdo->getLesFraisForfait($laFicheFrais['idVisiteur'], $laFicheFrais['mois']);
        if($lesFraisForfait[0]!=null) {
            include 'vues/v_Comptables/v_recapFrais_c.php';
            unset($_SESSION['lesFichesFrais']);
            break;
        }else{
            ajouterErreur('Il n\'y a pas de fiche de Frais pour ce visiteur ce mois');
            include "vues/v_erreurs.php";
            break;
        }

    case 'majFiche':
        $idVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
        $leMois = filter_input(INPUT_GET, 'leMois', FILTER_SANITIZE_STRING);
        $etatFrais = filter_input(INPUT_GET, 'etat', FILTER_SANITIZE_STRING);
       if ($etatFrais == 'CL') {
            $pdo->majEtatFicheFrais($idVisiteur, $leMois, 'VA');

        }
        if ($etatFrais == 'VA') {
            $pdo->majEtatFicheFrais($idVisiteur, $leMois, 'RB');
        }
        $montantValide=0;
        $lesFraisForfait= $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $lesFraisHorsForfait= $pdo->getLesFraisHorsForfait($idVisiteur,$leMois);
        $montantForfait= $pdo->getLesMontantForfait();
        foreach ($lesFraisForfait as $unFraisForfait){
            $montantValide+=($unFraisForfait["quantite"]*$montantForfait[$unFraisForfait["idfrais"]]);

        }
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait){
            if(!(substr($unFraisHorsForfait["libelle"],0,7)=="REFUSE ")){
                $montantValide+=$unFraisHorsForfait["montant"];
            }

        }
        $pdo->validerMontant($idVisiteur, $leMois, $montantValide);
        break;
}
$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idUser, $mois);
$lesFraisForfait = $pdo->getLesFraisForfait($idUser, $mois);
if ($_SESSION['role'] == 1) {
    require 'vues/v_listeFraisForfait.php';
    require 'vues/v_listeFraisHorsForfait.php';
}
