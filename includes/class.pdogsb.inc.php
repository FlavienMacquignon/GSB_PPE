<?php
/**
 * Classe d'accès aux données.
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsb_frais';
    private static $user = 'user';
    private static $mdp = 'secret';
    private static $monPdo;
    private static $monPdoGsb = null;

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,
            PdoGsb::$user,
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un utilisateur
     *
     * @param String $login Login de l'utilisateur
     * @param String $mdp Mot de passe de l'utilisateur
     *
     * @return mixed l'id, le nom, le prénom et le role sous la forme d'un tableau associatif
     */
    public function getInfosUser($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT userTable.idUserPK AS id, '
            . 'userTable.nom AS nom, '
            . 'userTable.prenom AS prenom, '
            . 'userTable.idRole AS role '
            . 'FROM gsb_frais.userTable '
            . 'WHERE userTable.login = :unLogin AND userTable.mdp = :unMdp'
        );
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }
    /**
     *  Retourne une liste de tous les visiteurs
     *
     * @return mixed l'id, le nom, le prénom sous la forme de d'un tableau associatif à deux dimensions
     */
    public function getTousLesVisiteurs()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT userTable.idUserPK AS id, '
            . 'userTable.nom AS nom, '
            . 'userTable.prenom AS prenom '
            . 'FROM gsb_frais_test.userTable '
            . 'WHERE userTable.idRole = 1'
        );
        $requetePrepare->execute();
        $k=0;
        $lesVisiteurs=array();
        while($uneLigne=$requetePrepare->fetch()){
            $id=$uneLigne['id'];
            $nom=$uneLigne['nom'];
            $prenom=$uneLigne['prenom'];
            $lesVisiteurs[$k]= array(
                'id' => $id,
                'nom'=>$nom,
                'prenom'=>$prenom
            );
            $k++;
        };
        return $lesVisiteurs;
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idUser     ID d'utilisateur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idUser, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM ligneFraisHorsForfait '
            . 'WHERE ligneFraisHorsForfait.idUserFK = :unIdUser '
            . 'AND ligneFraisHorsForfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * TODO documentation à remplir
     */
    public function getLeFraisHorsForfait($idFraisHorsForfait){
        $requetePrepare= PdoGsb::$monPdo->prepare(
            'SELECT ligneFraisHorsForfait.idUserFK AS visiteur, '
            .'ligneFraisHorsForfait.mois AS mois, '
            .'ligneFraisHorsForfait.libelle AS libelle, '
            .'ligneFraisHorsForfait.date AS date, '
            .'ligneFraisHorsForfait.montant AS montant '
            .'FROM ligneFraisHorsForfait '
            .'WHERE ligneFraisHorsForfait.idLigneFraisHorsForfaitPK = :idFraisHorsForfait '
        );
        $requetePrepare->bindParam(':idFraisHorsForfait', $idFraisHorsForfait, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idUser    ID de l'user
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idUser, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT ficheFrais.nbjustificatifs as nb FROM ficheFrais '
            . 'WHERE ficheFrais.idUserFK = :unIdUser '
            . 'AND ficheFrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idUser    ID de l'user
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idUser, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fraisForfait.idFraisForfaitPK as idfrais, '
            . 'fraisForfait.libelle as libelle, '
            . 'ligneFraisForfait.quantite as quantite '
            . 'FROM ligneFraisForfait '
            . 'INNER JOIN fraisForfait '
            . 'ON fraisForfait.idFraisForfaitPK = ligneFraisForfait.idFraisForfaitFK '
            . 'WHERE ligneFraisForfait.idUserFK = :unIdUser '
            . 'AND ligneFraisForfait.mois = :unMois '
            . 'ORDER BY ligneFraisForfait.idFraisForfaitFK'
        );
        $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fraisForfait.idFraisForfaitPK as idfrais '
            . 'FROM fraisForfait ORDER BY fraisForfait.idFraisForfaitPK'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idUser     ID de l'user
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idUser, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE ligneFraisForfait '
                . 'SET ligneFraisForfait.quantite = :uneQte '
                . 'WHERE ligneFraisForfait.idUserFK = :unIdUser '
                . 'AND ligneFraisForfait.mois = :unMois '
                . 'AND ligneFraisForfait.idFraisForfaitFK = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idUser          ID de l'user
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idUser, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET nbJustificatifs = :unNbJustificatifs '
            . 'WHERE ficheFrais.idUserFK = :unIdUser '
            . 'AND ficheFrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unNbJustificatifs', $nbJustificatifs, PDO::PARAM_INT);
        $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idUser ID de l'utilisateur
     * @param String $mois Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idUser, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT ficheFrais.mois FROM ficheFrais '
            . 'WHERE ficheFrais.mois = :unMois '
            . 'AND ficheFrais.idUserFK= :unIdUser'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }


    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idUser ID de l'user
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idUser)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM ficheFrais '
            . 'WHERE ficheFrais.idUserFK = :unIdUser'
        );
        $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idUser    ID de l'user
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idUser, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idUser);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idUser, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idUser, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'INSERT INTO ficheFrais (idUserFK, mois, nbjustificatifs,'
            . 'montantvalide, datemodif, idEtatFK) '
            . "VALUES (:unIdUser, :unMois, 0, 0, now(), 'CR')"
        );
        $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO ligneFraisForfait (idUserFK,mois,'
                . 'idFraisForfaitFK,quantite) '
                . 'VALUES(:unIdUser, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais['idfrais'], PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idUser     ID de l'user
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return null
     */
    public function creeNouveauFraisHorsForfait($idUser, $mois, $libelle, $date, $montant ) {
        $dateFr = dateFrancaisVersAnglais($date);
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'INSERT INTO ligneFraisHorsForfait '
            . 'VALUES (null, :unIdUser,:unMois, :unLibelle, :uneDateFr,'
            . ':unMontant) '
        );
        $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requetePrepare->execute();
    }

    /**
     * TODO peupler cette documentation
     * @return null
     */
    public function majFraisHF ($lesNouveauxFraisHF)
    {

        for ($i=0; $i<=sizeof($lesNouveauxFraisHF);$i++) {
            // FIXME vérifier la date

            $dateFr = dateFrancaisVersAnglais($lesNouveauxFraisHF[$i]['date']);
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'UPDATE ligneFraisHorsForfait '
                . 'SET  ligneFraisHorsForfait.libelle = :unLibelle, '
                . 'ligneFraisHorsForfait.date = :uneDateFr, '
                . 'ligneFraisHorsForfait.montant = :unMontant '
                . 'WHERE ligneFraisHorsForfait.idLigneFraisHorsForfaitPK = :unIdFraisHorsForfait '
                . 'AND ligneFraisHorsForfait.idUserFk = :unIdUser'
            );
            $leLibelle = $lesNouveauxFraisHF[$i]['libelle'];
            $leMontant= $lesNouveauxFraisHF[$i]['montant'];
            $idFrais=$lesNouveauxFraisHF[$i]['idLigneFraisHorsForfaitPK'];
            $idUser= $lesNouveauxFraisHF[$i]['idUserFK'];
            $requetePrepare->bindParam(':unLibelle', $leLibelle , PDO::PARAM_STR);
            $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMontant', $leMontant, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdFraisHorsForfait', $idFrais, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
            $requetePrepare->execute();
        }

    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM ligneFraisHorsForfait '
            . 'WHERE ligneFraisHorsForfait.idLigneFraisHorsForfaitPK = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idUser    ID d'un User
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idUser)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT ficheFrais.mois AS mois FROM ficheFrais '
            . 'WHERE ficheFrais.idUserFK = :unIdUser '
            . 'ORDER BY ficheFrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne tous les mois contenant une fiches de frais
     * @param none
     *
     * @return un tableau contenant les mois -aaaamm- où une fiche de frais est présente
     */
    public function getTousLesMoisDisponibles()
    {
        $requetePreapare = PdoGsb::$monPdo->prepare(
            'SELECT ficheFrais.mois AS mois '
            .'FROM ficheFrais '
            .'ORDER BY ficheFrais.mois desc '
        );
        $lesMois= array();
        $k=0;
        $requetePreapare->execute();
        while($laLigne=$requetePreapare->fetch()){
            $lesMois[$k]=$laLigne['mois'];
            $k++;
        }
        return $lesMois;

    }
    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idUser     ID de l'user
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idUser, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT ficheFrais.idEtatFK as idEtat, '
            . 'ficheFrais.dateModif as dateModif,'
            . 'ficheFrais.nbJustificatifs as nbJustificatifs, '
            . 'ficheFrais.montantValide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM ficheFrais '
            . 'INNER JOIN etat ON ficheFrais.idEtatFK = etat.idEtatPK '
            . 'WHERE ficheFrais.idUserFK = :unIdUser '
            . 'AND ficheFrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idUser     ID de l'User
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idUser, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET idEtatFK = :unEtat, dateModif = now() '
            . 'WHERE ficheFrais.idUserFK = :unIdUser '
            . 'AND ficheFrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
}
