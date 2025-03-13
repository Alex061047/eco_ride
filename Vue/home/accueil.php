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

<section class="container">
<div class="d-flex justify-content-center">
    <div class="col-6 mt-5 pt-5 px-5 text-center">
<h3 class="fw-light"><u>Qui sommes nous ?</u></h3> 
 <!--Texte-->
<div class="mt-3 pt-5">
    <p>
    Nec vox accusatoris ulla licet subditicii in his malorum quaerebatur acervis ut saltem specie tenus crimina 
    praescriptis legum committerentur, quod aliquotiens fecere principes saevi: sed quicquid Caesaris implacabilitati 
    sedisset, id velut fas iusque perpensum confestim urgebatur impleri.
    </p>
</div>
    </div>
    <!--Image 1-->
    <div class="col-6 mt-3 pt-5 px-5">
        <img class="img-fluid img-1" src="../../assets/images/Image1Bureau.jpg" alt="Image d'une route">
    </div>
</div>
</section>

<section class="container">
<div class="d-flex justify-content-center">
    <!--Image 2-->
<div class="col-6 mt-3 pt-5 px-5">
        <img class="img-fluid img-1" src="../../assets/images/Image2Bureau.jpg" alt="Image d'une route la nuit">
    </div>
    <!--Texte-->
    <div class="col-6 my-5 py-5 px-5 text-center">
    <p>
    Nec vox accusatoris ulla licet subditicii in his malorum quaerebatur acervis ut saltem specie tenus crimina 
    praescriptis legum committerentur, quod aliquotiens fecere principes saevi: sed quicquid Caesaris implacabilitati 
    sedisset, id velut fas iusque perpensum confestim urgebatur impleri.
    </p>
    </div>
</div>
</section>