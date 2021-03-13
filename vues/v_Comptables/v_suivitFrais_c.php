<?php
/**
 * Vue Fiche de Frais
 *
 * PHP Version 7
 *
 * @category PPE
 * @package GSB
 * @author Flavien Macquignon <flavien_macquignon@fastmail.fr>
 * @version GIT : <0>
 */
?>
<hr xmlns="http://www.w3.org/1999/html">
<h2 class="text-primary">Suivi des Paiements</h2>
<div>
    <form method="post" action="index.php?uc=gererFrais&action=recapFrais"
          role="form" id="form">
        <div id="hd-lstVisiteur" class="form-group">
            <label for="lstVisiteur" accesskey="n">Visiteur:</label>
            <select id="lstVisiteur" name="lstVisiteur" class="form-control">
                <?php
                $idVisiteur = -1;
                $lesVisiteurs=array();

                foreach ($lesFichesFrais as $ficheFrais) {
                    //WARN les visiteurs ne se suivent pas forcément
                    if ($idVisiteur != $ficheFrais['idVisiteur']) {
                        $idVisiteur = $ficheFrais['idVisiteur'];
                        $nom = $ficheFrais['nom'];
                        $prenom = $ficheFrais['prenom'];
                        ?>
                        <option value="<?php echo $ficheFrais['idVisiteur'] ?>">
                            <?php echo $nom . ' ' . $prenom ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <label id="hd_lstMois" accesskey="b">Mois: </label>
            <select id="lstMois" name="lstMois" class="form-control">
                <?php
                $Mois=-1;
                foreach ($lesFichesFrais as $ficheFrais){
                    if($Mois != $ficheFrais['mois']){
                        $Mois = $ficheFrais['mois'];
                        ?>
                        <option value="<?php echo $ficheFrais['mois'] ?>">
                            <?php echo $ficheFrais['mois'] ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
<input id="ok" type="submit" value="Valider" class="btn btn-success" role="button">
<input id="annuler" type="submit" value="Effacer" class="btn btn-danger" role="button">
    </form>
</div>