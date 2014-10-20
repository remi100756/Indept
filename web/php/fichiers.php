<?php
    require_once("fonctions.php");

    function ouvre($fichier) {
        $contenu = file_get_contents("../fichiers/$fichier.json");
        $json = json_decode($contenu, true);
        
        $est_proprietaire = $json["donneur"] == $_SESSION["utilisateur"]["login"] || $json["receveur"] == $_SESSION["utilisateur"]["login"];
        if($est_proprietaire || $json["partage"] == "public") {
            return $contenu;
        }
        
        return erreur("Impossible d'ouvrir le fichier.", "Vous n'avez pas les droits nécessaires pour lire ce fichier.");
    }
    
    function creer($info) {
        if( !minEntrees($info, array("nom","donneur")) )
            return json_encode("Arguments incorrects.", "Le fichier n'as pas pu être créé.");
        if( !isset($_SESSION["utilisateur"]["login"]) )
            return json_encode("Non connecté.", "Impossible de créer un fichier sans vous connecter.");
            
        $contenu = array(
            "nom" => $info["nom"],
            "creation_date" => time(),
            "derniere_edition" => time(),
            "donneur" => $info["donneur"],
            "receveur" => $_SESSION["utilisateur"]["login"],
            "partage" => isset($info["partage"]) ? $info["partage"] : "public",
            "liste" => array()
        );
            
        $receveur = $contenu["receveur"];
        $donneur =  $contenu["donneur"];
        $numFichier = 1;
        while( file_exists("../fichiers/$receveur-$donneur-$numFichier.json") ) {
            $numFichier++;
        }
        
        $contenu = json_encode($contenu, JSON_PRETTY_PRINT);
        file_put_contents("../fichiers/$receveur-$donneur-$numFichier.json", $contenu);
        return "$receveur-$donneur-$numFichier";
    }
    
    
    function modif($fichier, $nvContenu) {
        $contenu = ouvre($fichier);
        $jsonAnc = json_decode($contenu, true);
        $jsonNv  = json_decode($nvContenu, true);
        $est_proprietaire = $jsonAnc["donneur"] == $_SESSION["utilisateur"]["login"] || $jsonAnc["receveur"] == $_SESSION["utilisateur"]["login"];
        
        if(isErreur($contenu))
            return $contenu;
        if(!$est_proprietaire)
            return erreur("Permission non accordée.", "Vous n'etes pas propriétaire de ce fichier.");
            
        if( $json["receveur"] == $_SESSION["utilisateur"]["login"] ) {
            // Virer les trucs interdits
        }
        
        $jsonNv["derniere_edition"] = time();
        
        if(! file_put_contents("../fichiers/$fichier.json", json_encode($jsonNv, JSON_PRETTY_PRINT)) )
            return erreur("Impossible d'écrire dans le fichier.", "Vérifier les droits de PHP.");
        return $nvContenu;
    }
    
    
    function listerFichiers() {
        $liste = array();
        $dir = opendir("../fichiers");
        while($file = readdir($dir)) {
            if($file != "." && $file != "..") {
                $fichier = explode(".", $file)[0];
                $proprio = explode("-", $fichier);
                if( in_array($_SESSION["utilisateur"]["login"], $proprio) ) {
                    $liste[] = array(
                        "fichier" => $fichier,
                        "nom" => json_decode(ouvre($fichier), true)["nom"]
                    );
                }
            }
        }
        closedir($dir); 
        return json_encode($liste);
    }
    
    function getCSV($fichier) {
        $c = json_decode(ouvre($fichier), true);
        if( !$c )
            return false;
            
        $r = "";
        $r .= "nom;".$c["nom"] . ";receveur;".$c["receveur"] . ";donneur;".$c["donneur"] ."\n";
        
        foreach($c["liste"][0] as $key => $i) {
            $r .= "$key;";
        }
        $r .= "\n";
        foreach($c["liste"] as $i => $ligne) {
            foreach($ligne as $key => $val) {
                if( gettype($val) == "boolean" )
                    $r .= $val ? "oui" : "non";
                else
                    $r .= "$val;";
            }
            $r .= "\n";
        }
        
        return utf8_decode ($r);
    }
?>