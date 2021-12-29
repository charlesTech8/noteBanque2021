<?php 
require_once('connexion.php');

$user = formatChamp($_POST['user']);
$pass = formatPass($_POST['pass']);

if($connexion){
    if( isEmail( $user ) ){
        $sql =  'SELECT * FROM user WHERE email = :user AND pwd = :pass';
        $resultat = $connexion->prepare($sql);
        $resultat->execute(array(
            'user' => $user,
            'pass' => $pass
        ));
        if($resultat->rowCount() > 0 ){
            echo "success";
        }else{
            echo "echec";
        }$resultat->closeCursor();
    }else{
        echo 'Veuillez entrer un mail valide';
    }
}else{
    echo "Problème de connexion";
}
