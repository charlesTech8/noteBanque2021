<?php
require_once('connexion.php');

if( !empty( $_POST['numeroCompte'] ) && !empty( $_POST['solde'] ) ){
    $numeroCompte = formatChamp( $_POST['numeroCompte'] );
    $solde = formatChamp( $_POST['solde'] );
    $dateoperation = date('Y-m-d');

    //Verification etat du compte
    $sqlVerification = $connexion->prepare(
        'SELECT solde, etat FROM compte WHERE numeroCompte = :numeroCompte'
    );
    $sqlVerification->execute(array(
        'numeroCompte'  => $numeroCompte
    ));
    $resultat = $sqlVerification->fetch();
    $sqlVerification->closeCursor();
    if( $resultat != null ){
        if($resultat['etat'] == 'active'){
            //Ajoute du solde du client
            $newSolde = $resultat['solde'] + $solde;
            $sqlUpdate = $connexion->prepare(
                'UPDATE compte SET solde = :solde WHERE numeroCompte = '.$numeroCompte
            );
            try {
                $sqlUpdate->execute(array(
                    'solde' => $newSolde
                ));
                $sqlUpdate->closeCursor();
                
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
                    'typeop'        => 'depot',
                    'montant'       => $solde
                ));
                $sqlAddOperation->closeCursor();
                $sqlSelectInfo = $connexion->prepare(
                    'SELECT nom, prenom FROM client WHERE numeroCompte = '.$numeroCompte
                );
                $sqlSelectInfo->execute(array());
                $ans = $sqlSelectInfo->fetch();
                echo 'OPERATION SUCCES: NOM => '.$ans['nom']. ' PRENOM => '.$ans['prenom'].' DEPOT => '.$solde;
            } catch (Exception $e) {
                echo 'PROBLEME SURVENU';
            }
        }else{
            echo 'LE COMPTE EST BLOQUE';
        }
    }else{
        echo 'NUMERO DE COMPTE INVALIDE';
    }
}