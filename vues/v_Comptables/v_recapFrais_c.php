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

<hr>
<div class="panel panel-primary">
    <div class="panel-heading">Fiche de frais du mois
        <?php echo $laFicheFrais['mois'] ?> :
    </div>
    <div class="panel-body">
        <strong><u>Etat :</u></strong> <?php echo $laFicheFrais['libEtat'] ?>
        depuis le <?php echo $laFicheFrais['dateModif'] ?> <br>
        <strong><u>Montant validé :</u></strong> <?php echo $laFicheFrais['montantValide'] ?> <br>
        <!-- TODO faire de ceci un "vrai bouton stylisé -->
        <button class="btn btn-default"><a href="index.php?uc=gererFrais&action=majFiche&idVisiteur=<?php echo $laFicheFrais['idVisiteur']?>&leMois=<?php echo $laFicheFrais['mois']?>&etat=<?php echo $laFicheFrais['idEtat'] ?>">Mettre à jour la fiche</a></button>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">Eléments forfaitisés</div>
    <table class="table table-bordered table-responsive">
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $libelle = $unFraisForfait['libelle']; ?>
                <th> <?php echo htmlspecialchars($libelle) ?></th>
                <?php
            }
            ?>
        </tr>
        <tr>
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $quantite = $unFraisForfait['quantite']; ?>
                <td class="qteForfait"><?php echo $quantite ?> </td>
                <?php
            }
            ?>
        </tr>
    </table>
</div>
<div class="panel panel-info">
    <div class="panel-heading">Descriptif des éléments hors forfait -

        <?php echo $nbJustificatifs ?> justificatifs reçus
    </div>
    <table class="table table-bordered table-responsive">
        <tr>
            <th class="date" >Date</th>
            <th class="libelle">Libellé</th>
            <th class="montant">Montant</th>
        </tr>
        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $date = $unFraisHorsForfait['date'];
            $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
            $montant = $unFraisHorsForfait['montant']; ?>
            <tr>
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>