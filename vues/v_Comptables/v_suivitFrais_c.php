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
echo("vue Suivit Frais");
?>
<hr xmlns="http://www.w3.org/1999/html">
<h2 class="text-primary">Suivit des Paiements</h2>
<div>
    <!-- TODO vérifier l'action sur le formulaire -->
    <form method="post" action="index.php?uc=gererFrais&action=soumettreFrais"
          role="form" id="form">
        <div id="hd-lstVisiteur" class="form-group">
            <label for="lstVisiteur" accesskey="n">Visiteur:</label>
            <select id="lstVisiteur" name="lstVisiteur" class="form-control">
                <?php
                $idVisiteur = -1;
                foreach ($infoFicheFrais as $ficheFrais) {
                    if ($idVisiteur != $ficheFrais['idVisiteur']) {
                        $idVisiteur = $ficheFrais['idVisiteur'];
                        $nom = $ficheFrais['nom'];
                        $prenom = $ficheFrais['prenom'];
                        ?>
                        <option value="<?php echo $idVisiteur ?>">
                            <?php echo $nom . ' ' . $prenom ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>

        </div>
    </form>
</div>