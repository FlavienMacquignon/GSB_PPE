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
                $libelle = $unFraisForfait['libelle'];
                $idFrais = $unFraisForfait['idfrais']; ?>
                <label for="<?php echo $libelle ?>"><?php echo $libelle ?></label><br>
                <input type="number" id="<?php echo $libelle ?>" name="lesFraisForfait[<?php echo $idFrais ?>]"
                       value="<?php echo $unFraisForfait['quantite'] ?>"></input></br>
                <?php

            }
            ?>
            <!-- TODO créer un bouton de soumission de formulaire qui ne trigger une mise à jour
            que des FraisForfait en ne récupérant que certaines variables-->
            <button Class="btn btn-danger" id="Corriger" type="reset"> Réinitialiser
            </button>
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
                foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                    $idFraisHorsForfait = $unFraisHorsForfait['idLigneFraisHorsForfaitPK'];
                    $date = htmlspecialchars($unFraisHorsForfait['date']);
                    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                    $montant = htmlspecialchars($unFraisHorsForfait['montant']); ?>
                    <tr>
                        <td>
                            <label for="<?php echo $idFraisHorsForfait.'$'.$date ?>"></label>
                            <input type="text" id="<?php echo $idFraisHorsForfait.'$DATE' ?>"
                                   name="<?php echo $idFraisHorsForfait.'$DATE' ?>"
                                   value="<?php echo $date ?> ">
                        </td>
                        <td>
                            <label for="<?php echo $idFraisHorsForfait.'$'.$libelle ?>"></label>
                            <input type="text" id="<?php echo $idFraisHorsForfait .'$LIBELLE' ?>"
                                   name="<?php echo($idFraisHorsForfait .'$LIBELLE') ?>"
                                   value="<?php echo $libelle ?> ">
                        </td>
                        <td>
                            <label for="<?php echo $idFraisHorsForfait.'$'.$montant ?>"></label>
                            <input type="text" id="<?php echo $idFraisHorsForfait.'$MONTANT' ?>"
                                   name="<?php echo $idFraisHorsForfait.'$MONTANT' ?>"
                                   value="<?php echo $montant ?> ">
                        </td>
                        <!-- TODO inclure pour chaque ligne deux boutons-->
                    </tr>
                    <?php
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