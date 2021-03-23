<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/class.pdogsb.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/fct.inc.php';
try {
    $pdo = PdoGsb::getPdoGsb();
    $login = $_POST['login'];
    $mdp = $_POST['password'];
    try {
        $user = $pdo->getInfosUser($login, $mdp);
        if (isset($user)) {
            connecter($user['id'], $user['nom'], $user['prenom'], $user['role']);
        }
        if (!estConnecte()) {
            throw new Exception("Login ou mot de passe incorrect, veuillez réessayer");
        }
    } catch (Exception $exception) {
        print $exception->getMessage();
        die();
    }
    try {
        if ($_POST['operation'] == 'ADD') {
            try {
                $postDonne = $_POST['lesDonnes'];
                $lesFrais = json_decode($postDonne, true);
                if (!isset($lesFrais)) {
                    throw new Exception("Problèmes lors de la transmission des données, veuillez réessayer");
                }
            } catch (Exception $exception) {
                print $exception->getMessage();
                die();
            }
            $lesclesDonnes = array_keys($lesFrais);
            foreach ($lesclesDonnes as $unMois) {
                $lesFraisHf = $lesFrais[$unMois]['lesFraisHf'];
                $lesFraisForfait = array(
                    'etape' => $lesFrais[$unMois]['etape'],
                    'km' => $lesFrais[$unMois]['km'],
                    'nuitee' => $lesFrais[$unMois]['nuitee'],
                    'repas' => $lesFrais[$unMois]['repas']
                );
            }

            if ($pdo->estPremierFraisMois($user['id'], $lesclesDonnes[0])) {
                $pdo->creeNouvellesLignesFrais($user['id'], $lesclesDonnes[0]);
            }
            $pdo->majFraisForfait($user['id'], $lesclesDonnes[0], $lesFraisForfait);
            foreach ($lesFraisHf as $unFraisHF) {
                $laDate = $unFraisHF['jour'] . '/' . substr($lesclesDonnes[0], 4, 2) . '/' . substr($lesclesDonnes[0], 0, 4);
                $pdo->creeNouveauFraisHorsForfait($user['id'], $lesclesDonnes[0], $unFraisHF['motif'], $laDate, $unFraisHF['montant']);

            }
        } else {
            throw new Exception("L'opération a effectuer n'a pas été précisée, veuillez contacter votre administrateur système");
        }
    } catch (Exception $exception) {
        print $exception->getMessage();
        die();
    }
    print "Transaction effectuée avec succès";
} catch (Exception $exception){
    print $exception->getMessage();
    die();
}

?>