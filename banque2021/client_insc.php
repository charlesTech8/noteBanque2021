<?php
require_once('connexion.php');

if(!empty($_POST['nom'] ) && !empty( $_POST['prenom'] ) 
&& !empty($_POST['adresse']) && !empty($_POST['email']) && !empty($_POST['telephone']) && !empty($_POST['pwd'])){
    $numcompte = cle();
    $nom = formatChamp($_POST['nom']);
    $prenom = formatChamp($_POST['prenom']);
    $adresse = formatChamp($_POST['adresse']);
    $email = formatChamp($_POST['email']);
    $telephone = formatChamp($_POST['telephone']);
    $pwd = formatPass($_POST['pwd']);

    if(isEmail($email)){
        try {
            $sqlIns = $connexion->prepare(
                'INSERT INTO client(numeroCompte, nom, prenom, adresse, email, telephone, pwd) 
                VALUES (
                    :numeroCompte, :nom, :prenom, :adresse, :email, :telephone, :pwd
                )'
            );
            $sqlIns->execute(array(
                'numeroCompte'  => $numcompte,
                'nom'           => $nom,
                'prenom'        => $prenom,
                'adresse'       => $adresse,
                'email'         => $email,
                'telephone'     => $telephone,
                'pwd'           => $pwd
            ));
            $sqlIns->closeCursor();
            $sqlInsCompte = $connexion->prepare(
                'INSERT INTO compte(numeroCompte, solde, etat) 
                VALUES (
                    :numeroCompte, :solde, :etat
                )'
            );
            $sqlInsCompte->execute(array(
                'numeroCompte'  => $numcompte,
                'solde'         => 0,
                'etat'          => 'active'
            ));
            $sqlInsCompte->closeCursor();
            echo "Inscription succes";
        } catch (\Throwable $th) {
            echo "Probleme survenir lors de l'inscription. <br> Verifier les informations";
        }
    }else{
        echo "Veuillez entrer un mail valide";
    }
}else{
    echo "Veuillez remplir tous les champs";
}
