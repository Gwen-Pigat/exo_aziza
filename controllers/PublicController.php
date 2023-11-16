<?php
// Inclusion du fichier contenant la définition de la classe BeersModel
require_once 'repository/BeerRepository.php';


// Déclaration de la classe HomeController
class PublicController {
    
    private $colorRepository; 
    private $beerRepository; 
    // Constructeur de la classe HomeController
    public function __construct() {
        $this->beerRepository = new BeerRepository();
    }
    
    // Méthode index() qui sera appelée lors de l'accès à la page d'accueil
    public function index() {
        // Appel de la méthode getRandBeers() de l'objet BeersModel pour obtenir des bières aléatoires
        $beers = $this->beerRepository->findAll([], 5, 'RAND()');
        // // Inclusion de la view home correspondant à la page d'accueil
        require "views/accueil.view.php";
    }

    public function showBeerByColor($color) {
        $titre = "Toutes nos bières ". $color . 'S';
        $beers = $this->beerRepository->findAll([
            'NOM_COULEUR'=>$color
        ], 10);
        require "views/bieres.view.php";
    }

    public function showAllBeer() {
        $titre = "Toutes nos bières ";
        $beers = $this->beerRepository->findAll([
        ], 15, 'ID_ARTICLE DESC');
    
        require "views/bieres.view.php";
    }

    public function addBeer():void
    {
        $titre = "Ajouter une bière";

        $beer = [
            "NOM_ARTICLE"=>null,
            "PRIX_ACHAT"=>null,
            "VOLUME"=>null,
            "TITRAGE"=>null,
            "ID_ARTICLE"=>null
        ];
    
        require "views/bieres/add.view.php";
    }

    public function updateBeer(
        int $idBeer
    ):void
    {
        $beers = $this->beerRepository->findAll(['ID_ARTICLE'=>$idBeer], 1);
        if(empty($beers)){
            $error = "Résultat introuvable";
            require "views/bieres/add.view.php";
            exit;
        }
        if(!isset($beers[0])){
            $error = "Résultat introuvable";
            require "views/bieres/add.view.php";
            exit;
        }
        $beer = $beers[0];
        $titre = "Modifer la bière ".$beers[0]["NOM_ARTICLE"];
    
        require "views/bieres/add.view.php";
    }

    public static function wrapMessage(
        array|string $message,
        int $status=400
    )
    {
        $data = ["status"=>$status,"data"=>$message];
        if($status !== 200){
            $data["data"] = [
                "error"=>$message
            ];
        }
        return $data;
    }

    public function addBeerCall(
        array $post
    ):array
    {
        $keys = [
            "Nom de la bière"=>"NOM_ARTICLE",
            "Prix d'achat"=>"PRIX_ACHAT",
            "Volume"=>"VOLUME",
            "TITRAGE"=>"TITRAGE",
            "ID_COULEUR"=>"ID_COULEUR",
            "ID_MARQUE"=>"ID_MARQUE",
            "ID_TYPE"=>"ID_TYPE"
        ];
        $data = [
            "keys"=>[],
            "values"=>[],
            "bindings"=>[]
        ];

        $beer = null;
        if(isset($post["beer"]["ID_ARTICLE"])){
            $beers = $this->beerRepository->findAll(['ID_ARTICLE'=>$post["beer"]["ID_ARTICLE"]], 1);
            if(empty($beers)){
                return self::wrapMessage("Résultat introuvable", 404);
            }
            if(!isset($beers[0])){
                return self::wrapMessage("Résultat introuvable", 404);
            }
            $beer = $beers[0];
        }
        foreach($keys as $label=>$key){
            if(!isset($post["beer"][$key])){
                return self::wrapMessage("Le champ n'a pas pu être récupérée '".$label."'");
            }
            if($post["beer"][$key] === ""){
                return self::wrapMessage("Veuillez remplir le champ '".$label."'");
            }
            $data["keys"][] = $key;
            $data["values"][] = $post["beer"][$key];
            $data["bindings"][] = "?";
        }
        require "models/Beer.php";

        if($beer !== null){
            foreach($data["keys"] as $keyCount=>$key){
                $data["bindings"][$keyCount] = $key."=?";
            }
            $data["where"] = "ID_ARTICLE=?";
            $data["values"][count($data["values"])] = (int)$beer["ID_ARTICLE"];
            $beerModel = new Beer();
            if($beerModel->update(
                $data,
                "article"
            ) !== true){
                return self::wrapMessage("Une erreur a eut lieue lors de la mise à jour");
            }
            return self::wrapMessage([
                "redirect"=>[
                    "url"=>"../../blondes/",
                ],
                "message"=>"Mise à jour réussie"
            ], 200);
        }
        
        $beers = $this->beerRepository->findAll([], 1,"ID_ARTICLE DESC");

        $data["keys"][] = "ID_ARTICLE";
        $data["values"][] = $beers[0]["ID_ARTICLE"]+1;
        $data["bindings"][] = "?";

        $beerModel = new Beer();
        if($beerModel->create(
            $data,
            "article"
        ) !== true){
            return self::wrapMessage("Une erreur a eut lieue lors de la création");
        }
        return self::wrapMessage([
            "redirect"=>[
                "url"=>"../blondes/",
            ],
            "message"=>"Création réussie"
        ], 200);
    }

    public function deleteBeer($ID_ARTICLE){
      
        // $beer = $this->beerRepository->find($ID_ARTICLE);
        $beers = $this->beerRepository->findAll(['ID_ARTICLE'=>$ID_ARTICLE
        ], 15);
        
        if (empty($beers)){
            http_response_code(404);
            echo 'aucun résultat trouvé';
            exit;
        }
        if (!isset($beers[0])){
            http_response_code(404);
            echo 'aucun résultat trouvé';
            exit;
        }

        require "models/Beer.php";
        $beer = $beers[0];
        $beerModel = new Beer();
    
        if ($beerModel->delete('ID_ARTICLE', $ID_ARTICLE, 'article') !== true) {
            http_response_code(500);
            echo 'Erreur !';
            exit;

        }
        header("Status: 301 Moved Permanently", false, 301);
        header("Location: ".$_SERVER["HTTP_REFERER"]);

    }
}