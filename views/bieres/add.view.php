<?php 
ob_start();

$idForm = "setBeerNew";

$path = "../blondes/add/call";
if($beer["ID_ARTICLE"] !== null){
    $path = "../../blondes/update/".$beer["ID_ARTICLE"]."/call";
}

?>

<!--j'ai mis en place un buffer, une tamporisation via la fonction ob_start. La fonction ob_get_clean permet de déverser directement dans ma variable content tout ce qu'il y a entre les 2.    
Mon buffer se remplit entre les 2 fonctions ob_start et ob_get_clean puis se déverse dans la variable $content. Je vais pouvoir coder sans problème en html.

<h1>Toutes nos bières</h1> -->

<a href="../blondes/" class="btn btn-primary float-end mb-5">
    Retour à la liste
</a>
<div class="clearfix"></div>

<form id="<?= $idForm; ?>" data-submit="setBeerNewButton" data-path="<?= $path; ?>">
  <div class="mb-3">
    <label for="beer_nom_article" class="form-label">Nom de la bière *</label>
    <input type="text" class="form-control" id="beer_name" name="beer[NOM_ARTICLE]" value="<?= $beer["NOM_ARTICLE"]; ?>">
  </div>
  <div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">Prix d'achat *</label>
    <input type="number" class="form-control" id="beer_prix_achat" name="beer[PRIX_ACHAT]" step="0.01" value="<?= $beer["PRIX_ACHAT"]; ?>">
  </div>
  <div class="mb-3">
    <label for="beer_volume" class="form-label">Volume *</label>
    <input type="number" class="form-control" id="beer_volume" name="beer[VOLUME]" step="0.01" value="<?= $beer["VOLUME"]; ?>">
  </div>
  <div class="mb-3">
    <label for="beer_titrage" class="form-label">Titrage *</label>
    <input type="number" class="form-control" id="beer_volume" name="beer[TITRAGE]" step="0.01" value="<?= $beer["TITRAGE"]; ?>">
  </div>
  
  <input type="hidden" name="beer[ID_COULEUR]" value=1 />
  <input type="hidden" name="beer[ID_MARQUE]" value=1 />
  <input type="hidden" name="beer[ID_TYPE]" value=1 />

    <?php if($beer["ID_ARTICLE"] !== null){ ?>
        <input type="hidden" name="beer[ID_ARTICLE]" value="<?= $beer["ID_ARTICLE"]; ?>" />
    <?php } ?>


  <button type="submit" id="setBeerNewButton" class="btn btn-primary d-block w-100">Sauvegarder</button>
</form>

<script>
    let loginForm = document.getElementById("<?= $idForm; ?>");
    loginForm.addEventListener("submit", (e) => {
        e.preventDefault();
        let formSubmit = e.currentTarget
        let buttonSubmit = document.getElementById(formSubmit.getAttribute("data-submit"))
        buttonSubmit.disabled = true
        let formData = new FormData(e.currentTarget)
        console.log(formData, e.currentTarget)
    
        let path = formSubmit.getAttribute("data-path")
        if(path === null){
            alert("Route introuvable")
            return
        }

        fetch(path, { 
            method: 'POST', 
            body: new FormData(formSubmit) 
        })
        .then(Result => Result.json())
        .then(data => {
            buttonSubmit.disabled = false
            let response = data.data
            console.log(response, data)
            if(response.error){
                alert(response.error);
                return
            }
            if(response.message){
                if(this.hasResultsTarget){
                    toast(response.message,"bg-success", false);
                } else{
                    alert(response.message,"success");
                }
            }
            if(response.content){
                document.getElementById(response.content.divID).innerHTML = response.content.divContent
                loadImages()
            }
            if(response.animate){
                window.scrollTo({top: 0, behavior: 'smooth'});
            }
            if(response.redirect){
                let timer = 0
                if(response.redirect.timer !== null){
                    timer = response.redirect.timer
                }
                setTimeout(function(){
                    window.location = response.redirect.url
                },timer);
            }
            emptyForm(formSubmit)
        })
        .catch(errorMsg => {
            buttonSubmit.disabled = false
        });

    });


</script>

<?php

$content = ob_get_clean();
require "./views/template.php";
?>

