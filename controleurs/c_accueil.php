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

if ($estConnecte) {
    if ($_SESSION['role']==1) {
        include 'vues/v_accueil.php';
    }
    else{
        include 'vues/v_Comptables/v_accueil_c.php';
    }
} else {
    include 'vues/v_connexion.php';
}
