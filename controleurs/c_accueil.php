<?php
/**
 * Gestion de l'accueil
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
// FIXME ce test n'est pas bon, la page n'est jamais chargé (Boucle vers c_connexion) ==>(peut-etre un erreur au niveau de $_SESSION['role'] ?)
if ($estConnecte) {
    if ($_SESSION['role']==1) {
        include 'vues/v_accueil.php';
    }
    else{
        include 'vues/v_Comptables/v_accueil.php';
    }
} else {
    include 'vues/v_connexion.php';
    echo("include connexion");
}
