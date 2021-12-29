<?php
require_once('connexion.php');
if( !empty( $_POST['numeroCompte'] ) && !empty( $_POST['solde'] ) && !empty( $_POST['pass'] )){
    $numeroCompte = formatChamp( $_POST['numeroCompte'] );
    $solde = formatChamp( $_POST['solde'] );
    $pass = formatPass( $_POST['pass'] );
    $dateoperation = date('Y-m-d');
    if( isActive( $numeroCompte, $pass ) ){
        $soldeCompte = getSolde( $numeroCompte );
        if( $soldeCompte < $solde ){
            echo 'RETRAIT IMPOSSIBLE (SOLDE INSUFFISANT)';
        }else{
            $newSolde = $soldeCompte - $solde;
            $sqlUpdateCompte = $connexion->prepare(
                'UPDATE compte SET solde = :solde WHERE numeroCompte = '.$numeroCompte
            );
            try {
                $sqlUpdateCompte->execute(array(
                    'solde' => $newSolde
                ));
                $sqlUpdateCompte->closeCursor();

                //Ajoute de l'operation
                $sqlAddOperation = $connexion->prepare(
                    'INSERT INTO operation(dateop, numeroCompte, typeOperation, montant) 
                    VALUES (
                        :dateop, :numeroCompte, :typeop, :montant
                    )'
                );
                $sqlAddOperation->execute(array(
                    'dateop'        => $dateoperation,
                    'numeroCompte'  => $numeroCompte,
                    'typeop'        => 'retrait',
                    'montant'       => $solde
                ));
                $sqlAddOperation->closeCursor();
                $sqlSelectInfo = $connexion->prepare(
                    'SELECT nom, prenom FROM client WHERE numeroCompte = '.$numeroCompte
                );
                $sqlSelectInfo->execute(array());
                $ans = $sqlSelectInfo->fetch();
                echo 'OPERATION SUCCES: NOM => '.$ans['nom']. ' PRENOM => '.$ans['prenom'].' RETRAIT => '.$solde;
            } catch (Exception $e) {
                echo 'RETRAIT IMPOSSIBLE (UN PROBLEME SURVENU)';
            }
        }
    }else{
        echo 'RETRAIT IMPOSSIBLE (COMPTE BLOQUE ou NUMERO INVALIDE)';
    }
}