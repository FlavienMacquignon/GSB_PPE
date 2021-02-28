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
                        <option value="<?php echo $idVisiteur ?>" onclick="reveal()">
                            <?php echo $nom . ' ' . $prenom ?>
                        </option>
                        <?php
                    } ?>
                </select>
            </div>
            <script>
                function reveal() {
                    document.getElementById("hd-lstMois").hidden = false;

                }
            </script>
            <div id="hd-lstMois" class="form-group" hidden>
                <label for="lstMoisVisiteur" accesskey="n">Mois: </label>
                <select id="lstMoisVisiteur" name="lstMoisVisiteur" class="form-control">
                    <?php
                    foreach ($lesVisiteurs as $unVisiteur) {
                        foreach ($lesMois[$unVisiteur['id']] as $unMois) {
                            $mois = $unMois['mois'];
                            $numAnnee = $unMois['numAnnee'];
                            $numMois = $unMois['numMois'];
                            if ($mois == $moisASelectionner) {
                                ?>
                                <option selected value="<?php echo $mois ?>">
                                    <?php echo $numMois . '/' . $numAnnee ?>
                                </option>
                            <?php } else { ?>
                            <option value="<?php echo $mois ?>">
                                <?php echo $numMois . '/' . $numAnnee ?>
                                </option><?php
                            }
                        }
                    }
                    ?>

                </select>
            </div>
        </form>
    </div>
</div>