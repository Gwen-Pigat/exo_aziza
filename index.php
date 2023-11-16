<?php

// déf de la constante url qui permettra d'accéder à toutes les ressources en repartant de la racine du site, on construit les routes pour les crud
define("URL", str_replace("index.php","",(isset($_SERVER['HTTPS']) ? "https" : "http"). // ternaire pour mettre soit https soit http
"://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]"));

require_once "controllers/PublicController.php";
require_once "models/CoreModel.php";

if(empty($_GET['page'])){

    require "views/accueil.view.php";

}else{

    //on explose l'url à partir du / et on filtre le nom de la page de manière sécurisée avec la fonction FILTER_SANITIZE_URL (ça supprime tous les caractères sauf les lettres, chiffres et certains caractères spéciaux)
    $url = explode("/", filter_var($_GET['page']), FILTER_SANITIZE_URL);
    // echo "<pre>"; // pour un meilleur formatage
    // print_r($url);
    // echo"</pre>";
    // var_dump($url);

    $_GET ['page'] = $url[0];

    switch($_GET['page']){
        case "accueil" : 
            $controller = new PublicController();
            $controller->index();
        break;
       
        case "apropos" : require "views/apropos.view.php";
        break;

        case "origine" : require "views/origine.view.php";
        break;
       
        case "clients" : require "views/clients.view.php";
        break;

        case "blondes" :
            $controller = new PublicController();
            if (isset($url[1]) && $url[1] !== ""){
                if($url[1] === "add"){
                    /* Call AJAX */
                    if (isset($url[2]) && $url[2] === "call"){
                        echo json_encode($controller->addBeerCall($_POST));exit;
                    }
                    $controller->addBeer();
                    break;
                }
                if (isset($url[2]) && $url[2]==='delete'){
                    $controller->deleteBeer($url[1]);
                
                }
                /* Call AJAX */
                if (isset($url[1]) 
                && $url[1]==='update' 
                && isset($url[2]) 
                && isset($url[3]) && $url[3] === "call"){
                    echo json_encode($controller->addBeerCall($_POST));exit;
                }
                if (isset($url[2]) && $url[2]==='update'){
                    $controller->updateBeer($url[1]);
                }
                break;
            }
            $controller->showBeerByColor('Blonde');
        break;

        case "brunes" : 
            $controller = new PublicController();
            $controller->showBeerByColor('Brune');
        break;

        case "blanches" : 
            $controller = new PublicController();
            $controller->showBeerByColor('Blanche');
        break;

        case "ambrees" : 
            $controller = new PublicController();
            $controller->showBeerByColor('Ambree');
        break;

        case "toutes" : 
            $controller = new PublicController();
            $controller->showAllBeer();
        // case "toutes" : require "views/toutes.view.php";
        break;

        case "nouvelleCouleur" : 
            require "views/nouvelleCouleur.view.php";
            // $controller = new PublicController();
            // $controller->showBeerByColor(': nouvelles Couleur');
        break;

        case "nouvelleBiere" : 
            require "views/nouvelleBiere.view.php";
            // $controller = new PublicController();
            // $controller->showBeerByColor(': nouvelles Biere');
        break;

    }
}

?>