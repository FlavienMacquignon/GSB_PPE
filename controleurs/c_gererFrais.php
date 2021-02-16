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
    // FIXME ne fonctionne pas lorsque le role est visiteur mais fonctionne pour les comptables
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

case 'validerFrais':

        break;
}
$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idUser, $mois);
$lesFraisForfait = $pdo->getLesFraisForfait($idUser, $mois);
require 'vues/v_listeFraisForfait.php';
require 'vues/v_listeFraisHorsForfait.php';
