<?php
/**
 *Vue Valider Frais Comptable
 *
 * PHP Version 7
 *
 * @category PPE
 * @package GSB
 * @author Flavien Macquignon <flavien.macquignon@fastmail.fr>
 */
?>
<!-- TODO Créer un deuxième formulaire de sélection du Mois lorsque le visiteur est sélectionné, permettra de contourner le problème. -->
<h2>Valider les fiches de Frais</h2>
<div class="row">
    <div class="col-md-4">
        <h3>Sélectionner un visiteur: </h3>
    </div>
    <div class="col-md-4">
        <form action="index.php?uc=gererFrais&action=validerFrais"
              method="post" role="form">
            <div class="form-group">
                <label for="lstVisiteur" accesskey="n">Visiteur: </label>
                <select id="lstVisiteur" name="lstVisiteur" class="form-control">
                    <?php
                    foreach ($lesVisiteurs as $unVisiteur) {
                        $idVisiteur = $unVisiteur['id'];
                        $prenom = $unVisiteur['prenom'];
                        $nom = $unVisiteur['nom'];
                        ?>
                        <option value="<?php echo $idVisiteur ?>">
                            <?php echo $prenom . ' ' . $nom ?>
                        </option>
                        <?php
                    } ?>
                </select>
            </div>
        </form>
    </div>
</div>