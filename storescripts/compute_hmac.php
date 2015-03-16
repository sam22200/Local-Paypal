<?php
  function computeHmac($PBX_SITE, $PBX_RANG, $PBX_IDENTIFIANT, $PBX_TOTAL, $PBX_DEVISE, $PBX_CMD, $PBX_PORTEUR, $PBX_RETOUR, $PBX_HASH,  $PBX_TIME ){
    // On récupère la date au format ISO-8601
    //$dateTime = date("c");
    // On crée la chaîne à hacher sans URLencodage
    $msg = "";

    // On récupère la clé secrète HMAC (stockée dans une base de données par exemple) et que l’on
    //renseigne dans la variable $keyTest;
    // Si la clé est en ASCII, On la transforme en binaire
    $binKey = pack("H*", $keyTest);

    // On calcule l’empreinte (à renseigner dans le paramètre PBX_HMAC) grâce à la fonction hash_hmac et
    // la clé binaire
    // On envoie via la variable PBX_HASH l'algorithme de hachage qui a été utilisé (SHA512 dans ce cas)
    // Pour afficher la liste des algorithmes disponibles sur votre environnement, décommentez la ligne
    // suivante
    // print_r(hash_algos());
    $hmac = strtoupper(hash_hmac('sha512', $msg, $binKey));
    // La chaîne sera envoyée en majuscules, d'où l'utilisation de strtoupper()
    // On crée le formulaire à envoyer à Paybox System
    // ATTENTION : l'ordre des champs est extrêmement important, il doit
    // correspondre exactement à l'ordre des champs dans la chaîne hachée
    return $hmac;
  }
?>
