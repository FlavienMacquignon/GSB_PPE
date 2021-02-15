<?php
/**
 * Gestion de la connexion
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

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if (!$uc) {
    $uc = 'demandeconnexion';
}

switch ($action) {
case 'demandeConnexion':
    include 'vues/v_connexion.php';
    break;
case 'valideConnexion':
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $mdp = filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_STRING);

    $user = $pdo->getInfosUser($login, $mdp);
    if (!is_array($user)) {
        ajouterErreur('Login ou mot de passe incorrect');
        include 'vues/v_erreurs.php';
        include 'vues/v_connexion.php';
    } else {
        $id = $user['id'];
        $nom = $user['nom'];
        $prenom = $user['prenom'];
        $role = $user['role'];
        connecter($id, $nom, $prenom, $role);
        // WARN Regarder ceci, que fait "header()" exactement? Une simple redirection? Pourquoi "Location: index.php"
        // WARN la valeur de $uc n'est pas modifiée ici, elle correspond donc toujours à ce que lui a attribué v_connexion.php ("connexion")? ==> Comment est-t'on redirigé après ça?
        // WARN v_entete.php intègre un test pour savoir si l'utilisateur est connecté; cela modifie le retour ?
        header('Location: index.php');
    }
    break;
default:
    include 'vues/v_connexion.php';
    break;
}
