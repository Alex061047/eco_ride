<link rel="stylesheet" href="../../assets/styles/home/home.css">
   
<header>
<div class="hero-scene text-center text-black">
    <div class="hero-scene-content">
        <h1>EcoRide</h1>
    </div>
</div>
</header>

<section class="text-center text-black">
    <h3 class="fw-light"><u>Itinéraires</u></h3>

    <!-- Formulaire de recherche -->
    <form class="row justify-content-center mt-3">

    <!--Rajout d'un bloc vide pour prendre dela place et aligner les blocs-->
   <div class="col-sm-1"></div>

    <!-- Sélection du jour -->
    <div class="col-sm-2">
    <input type="text" class="form-control" id="jour" name="jour" placeholder="Jour" 
        onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'" required>
</div>

    <!-- Ville de départ -->
    <div class="col-sm-2">
        <input type="text" class="form-control" id="depart" name="depart" placeholder="Ville de départ" required>
    </div>

    <!-- Ville d'arrivée -->
    <div class="col-sm-2">
        <input type="text" class="form-control" id="arrivee" name="arrivee" placeholder="Ville d'arrivée" required>
    </div>

    <!-- Bouton Rechercher -->
    <div class="col-sm-auto">
        <button type="submit" class="btn btn-success w-100">Rechercher</button>
    </div>

</form>
</section>