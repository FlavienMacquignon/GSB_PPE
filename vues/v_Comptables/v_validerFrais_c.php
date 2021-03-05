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
                        <option value="<?php echo $idVisiteur ?>" >
                            <?php echo $nom . ' ' . $prenom ?>
                        </option>
                        <?php
                    } ?>
                </select>
            </div>
            <!-- FIXME mettre cette dropdown "aside" -->
            <div id="hd-lstMois" class="form-group">
                <label for="lstMoisVisiteur" accesskey="n">Mois: </label>
                <select id="lstMoisVisiteur" name="lstMoisVisiteur" class="form-control">
                    <?php
                        foreach ($lesMois as $unMois)
                        {
                            $numAnnee= $unMois[0];
                            $numMois= $unMois[0];
                            $numAnnee= substr($numAnnee,0, 4);
                            $numMois= substr($numMois,4);

                        if ($unMois == $moisASelectionner) {
                            ?>
                            <option selected value="<?php echo $moisASelectionner ?>">
                                <?php echo $numMois . '/' . $numAnnee ?>
                            </option>
                        <?php } else { ?>
                        <option value="<?php echo $mois ?>">
                            <?php echo $numMois . '/' . $numAnnee ?>
                            </option><?php
                        }
                    }
                    ?>

                </select>
            </div>
            <input id="ok" type="submit" value="Valider" class="btn btn-success" role="button">
            <input id="annuler" type="submit" value="Effacer" class="btn btn-danger" role="button">
        </form>
    </div>
</div>