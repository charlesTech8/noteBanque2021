<?php
require_once('connexion.php');
if( !empty( $_POST['numeroCompte1'] ) && !empty( $_POST['solde'] ) && !empty( $_POST['pass'] ) && !empty( $_POST['numeroCompte2'] ) ){
    $numeroCompte1 = formatChamp( $_POST['numeroCompte1'] );
    $numeroCompte2 = formatChamp( $_POST['numeroCompte2'] );
    $solde = formatChamp( $_POST['solde'] );
    $pass = formatPass( $_POST['pass'] );
    $dateoperation = date('Y-m-d');
    if( isActive( $numeroCompte1, $pass ) ){
        if( isCompte( $numeroCompte2 ) ){
            $soldeCompte = getSolde( $numeroCompte1 );
            if( $soldeCompte < $solde ){
                echo 'TRANSFERT IMPOSSIBLE (SOLDE INSUFFISANT)';
            }else{
                $soldeCompte2 = getSolde( $numeroCompte2 );
                $newSolde = $soldeCompte2 + $solde;
                $sqlUpdateCompte = $connexion->prepare(
                    'UPDATE compte SET solde = :solde WHERE numeroCompte = '.$numeroCompte2
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
                        'numeroCompte'  => $numeroCompte1,
                        'typeop'        => 'transfert',
                        'montant'       => $solde
                    ));
                    $sqlAddOperation->closeCursor();
                    $sqlSelectInfo = $connexion->prepare(
                        'SELECT nom, prenom FROM client WHERE numeroCompte = '.$numeroCompte1
                    );
                    $sqlSelectInfo->execute(array());
                    $ans = $sqlSelectInfo->fetch();
                    echo 'TRANSFERT SUCCES: SENDEUR => '.$numeroCompte1.' NOM => '.$ans['nom']. ' PRENOM => '.$ans['prenom'].' TRANSFERT => '.$solde.' RECEVEUR => '.$numeroCompte2;
                } catch (Exception $e) {
                    echo 'TRANSFERT IMPOSSIBLE (UN PROBLEME SURVENU)';
                }
            }
        }else{
            echo 'TRANSFERT IMPOSSIBLE (COMPTE DU RECEVEUR BLOQUE ou NUMERO INVALIDE)';
        }
    }else{
        echo 'TRANSFERT IMPOSSIBLE (COMPTE DU SENDEUR BLOQUE ou NUMERO INVALIDE)';
    }
}