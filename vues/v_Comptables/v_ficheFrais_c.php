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
<!-- <hr> est utilisé ici pour marquer le changement entre les deux parties de la page (cf v_etatFrais.php)-->
<hr xmlns="http://www.w3.org/1999/html">
<h2 class="text-primary">Valider la fiche de frais</h2>
<div>
    <form method="post" action="index.php?uc=gererFrais&action=soumettreFrais"
          role="form" id="form">
        <div>
            <div>Eléments forfaitisés</div>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $libelle = $unFraisForfait['libelle']; ?>
                <label for="<?php echo $libelle ?>"><?php echo $libelle ?></label><br>
                <input type="number" id="<?php echo $libelle ?>" name="<?php echo $libelle ?>"
                       value="<?php echo $unFraisForfait['quantite'] ?>"></input></br>
                <?php
                // TODO inclure deux boutons ici (Corriger et Réinitialiser)
            }
            ?>
            <button Class="btn btn-danger" id="Corriger" type="reset"">Réinitialiser</button>
        </div>
        </br>
        </br>
        <div class="panel panel-info">
            <div class="panel-heading">Descriptif des éléments hors forfait -
                <?php echo $nbJustificatifs ?> justificatifs reçus
            </div>
            <table class="table table-bordered table-responsive">
                <tr>
                    <th class="date">Date</th>
                    <th class="libelle">Libellé</th>
                    <th class='montant'>Montant</th>
                </tr>
                <?php
                $k = 0;
                foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                    $date = $unFraisHorsForfait['date'];
                    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                    $montant = $unFraisHorsForfait['montant']; ?>
                    <tr>
                        <td>
                            <label for="<?php echo $date ?>"></label>
                            <input type="text" id="<?php echo $date ?>"
                                   name="<?php echo("FraisHorsForfait" . $k) ?>"
                                   value="<?php echo $date ?> ">
                        </td>
                        <td>
                            <label for="<?php echo $libelle . $date ?>"></label>
                            <input type="text" id="<?php echo $libelle . $date ?>"
                                   name="<?php echo("FraisHorsForfait" . $k) ?>" value="<?php echo $libelle ?> ">
                        </td>
                        <td>
                            <label for="<?php echo $montant . $date ?>"></label>
                            <input type="text" id="<?php echo $montant . $date ?>"
                                   name="<?php echo("FraisHorsForfait" . $k) ?>" value="<?php echo $montant ?>">
                        </td>
                        <!-- TODO inclure pour chaque ligne deux boutons-->
                    </tr>
                    <?php
                    $k++;
                }
                ?>
            </table>
        </div>
        </br>
        </br>
        <button class="btn btn-success" id="submit_btn" type="submit" value="uc=">Valider</button>
        <button class="btn btn-danger" id="btn_corriger_page" type="reset">Réinitialiser</button>
    </form>
    <!-- TODO inclure deux boutons ici, corriger et réinitialiser -->
</div>