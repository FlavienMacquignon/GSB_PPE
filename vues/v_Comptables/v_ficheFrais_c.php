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
<hr>
<div>
    <div>Eléments forfaitisés</div>
    <form><?php
        foreach ($lesFraisForfait as $unFraisForfait){
            $libelle= $unFraisForfait['libelle']; ?>
        <label for="<?php echo $libelle?>"><?php echo $libelle?></label><br>
        <input type="number" id="<?php echo $libelle?>" name="<?php echo $libelle?>"
               value="<?php echo $unFraisForfait['quantite']?>"></input>
    <?php
        }
    ?></form>
</div>