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

// FIXME style temporaire
?>

<hr>
<div class="panel panel-primary">
    <div class="panel-heading">Fiche de frais du mois
        <?php echo $laFicheFrais['mois'] ?> :
    </div>
    <div class="panel-body" style="color: black">
        <strong><u>Etat :</u></strong> <?php echo $laFicheFrais['libEtat'] ?>
        depuis le <?php echo $laFicheFrais['dateModif'] ?> <br>
        <strong><u>Montant validé :</u></strong> <?php echo $laFicheFrais['montantValide'] ?> <br>
        <button><a href="index.php?uc=gererFrais&action=majFiche&idVisiteur=<?php echo $laFicheFrais['idVisiteur']?>&leMois=<?php echo $laFicheFrais['mois']?>&etat=<?php echo $laFicheFrais['idEtat'] ?>">Mettre à jour la fiche</a></button>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">Eléments forfaitisés</div>
    <table class="table table-bordered table-responsive">
        <tr style="color: black">
            <?php
            foreach ($lesFraisForfait as $unFraisForfait) {
                $libelle = $unFraisForfait['libelle']; ?>
                <th> <?php echo htmlspecialchars($libelle) ?></th>
                <?php
            }
            ?>
        </tr>
        <tr style="color: black">
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
            <th class="date" style="color: black">Date</th>
            <th class="libelle" style="color: black">Libellé</th>
            <th class='montant' style="color: black">Montant</th>
        </tr>
        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $date = $unFraisHorsForfait['date'];
            $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
            $montant = $unFraisHorsForfait['montant']; ?>
            <tr>
                <td style="color: black"><?php echo $date ?></td>
                <td style="color: black"><?php echo $libelle ?></td>
                <td style="color: black"><?php echo $montant ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</div>