<?php 
$db = "banque2021";
$host = "localhost";

try {
    $connexion = new PDO(
        'mysql:host=localhost; dbname=banque2021', 
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (Exception $e) {
    die('Erreur'. $e->getMessage());
}

/**
 * Génération du numéro de compte
 */
function cle():String{
    $longueCle = 15;
    $cle = "";
    for ($i=1; $i < $longueCle ; $i++) { 
        $cle .=mt_rand(0,9);
    }
    return $cle;
}

function formatChamp(String $valchamps): String{
    return htmlentities(addslashes($valchamps));
}

function formatPass(String $pass): String{
    return md5(sha1( $pass ));
}

function isEmail(String $mail): bool{
    if(!filter_var($mail, FILTER_VALIDATE_EMAIL))
        return false;
    else
        return true;
}

/**
 * Verification si le compte est active
 */
function isActive( int $numeroCompte, String $pass ):bool{
    global $connexion;
    //Verification etat du compte
    $sqlVerification = $connexion->prepare(
        'SELECT * FROM client, compte WHERE client.numeroCompte = compte.numeroCompte AND client.numeroCompte = :numeroCompte AND pwd = :pass AND etat = :etat'
    );
    $sqlVerification->execute(array(
        'numeroCompte'  => $numeroCompte,
        'pass'          => $pass,
        'etat'          => 'active'
    ));
    if( $sqlVerification->rowCount() > 0 )
        return true;
    else
        return false;
    $sqlVerification->closeCursor();
}

function isCompte( $numeroCompte ):bool{
    global $connexion;
    //Verification etat du compte
    $sqlVerification = $connexion->prepare(
        'SELECT * FROM compte WHERE numeroCompte = :numeroCompte AND etat = :etat'
    );
    $sqlVerification->execute(array(
        'numeroCompte'  => $numeroCompte,
        'etat'          => 'active'
    ));
    if( $sqlVerification->rowCount() > 0 )
        return true;
    else
        return false;
    $sqlVerification->closeCursor();
}

/**
 * Solde d'un client
 */
function getSolde( int $numeroCompte ):int{
    global $connexion;
    $sqlVerification = $connexion->prepare(
        'SELECT solde FROM compte WHERE numeroCompte = :numeroCompte'
    );
    $sqlVerification->execute(array(
        'numeroCompte'  => $numeroCompte,
    ));
    $resultat = $sqlVerification->fetch();
    if( $resultat != null )
        return $resultat['solde'];
    $sqlVerification->closeCursor();
}