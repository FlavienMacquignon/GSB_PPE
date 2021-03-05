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
        $pdo->supprimerFraisHorsForfait($idFrais);
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
        // FIXME  ajouter un test sur les valeurs en retour et throw une page d'erreur si les valeurs sont vides
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($leVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($leVisiteur, $leMois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($leVisiteur, $leMois);
        $numAnnee = substr($leMois, 0, 4);
        $numMois = substr($leMois, 4, 2);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        //FIXME modifier l'include ici pour pouvoir effectuer des modifications dans la fiche de frais
        include 'vues/v_Comptables/v_ficheFrais_c.php';
        //TODO ajouter la possibilité de supprimer des frais hors forfait
        //TODO ajouter la possibilité de modifier les frais forfaitisés
        //TODO ajouter la possibilité de valider la fiche ==> C'est un immense formulaire==> La fiche est passée à l'état "Validée"
        break;

    case "soumettreFrais":
        echo("fiche_validée");
        break;
}
$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idUser, $mois);
$lesFraisForfait = $pdo->getLesFraisForfait($idUser, $mois);
if ($_SESSION['role'] == 1) {
    require 'vues/v_listeFraisForfait.php';
    require 'vues/v_listeFraisHorsForfait.php';
}
