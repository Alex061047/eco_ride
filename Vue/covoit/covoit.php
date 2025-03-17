<link rel="stylesheet" href="../../assets/styles/covoit/covoit.css">
   
<header>
<div class="hero-scene text-center text-black">
    <div class="hero-scene-content">
        <h1>Covoiturage</h1>
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


<section>
    <div class="container mt-4">
        
        <!--Filtres-->

        <div class="d-flex align-items-center gap-3 filtre">
        <div>
            <h5 class="fw-light"><u>Filtres :</u></h5>
        </div>

        <div class="d-flex align-items-center">
            <label for="mention" class="me-1">Mention écologique</label>
            <input type="checkbox" id="mention" name="mention"/>
        </div>

        <div class="d-flex align-items-center">
            <label for="prix" class="me-1">Prix maximum</label>
            <input type="number" id="prix" name="prix" class="form-control" style="width: 80px;"/>
        </div>

        <div class="d-flex align-items-center">
            <label for="duree" class="me-1">Durée maximum</label>
            <input type="time" id="duree" name="duree" class="form-control" style="width: 120px;"/>
        </div>

        <div class="d-flex align-items-center text-nowrap">
            <label for="animaux" class="me-1">Animaux acceptés</label>
            <select id="animaux" class="form-select">
                <option value=""></option>
                <option value="oui">Oui</option>
                <option value="non">Non</option>
            </select>
        </div>

        <div class="d-flex align-items-center text-nowrap">
            <label for="note" class="me-1">Note minimum</label>
            <select id="note" class="form-select">
                <option value=""></option>
                <option value="1">1/5</option>
                <option value="2">2/5</option>
                <option value="3">3/5</option>
                <option value="4">4/5</option>
                <option value="5">5/5</option>
            </select>
        </div>

        </div>
   

        <!-- Liste des covoiturages -->
        <div class="row mt-2 justify-content-center">
        
            <!--Profil-->
                <div class="card rounded col-md-12 p-3 mb-3">

                    <div class="row text-center">

                        <div class="col-2">
                    <h5 class="fw-light"><u>Pseudo</u></h5>
                    <img class="img-fluid profil rounded-circle" src="../../assets/images/Persona/Persona 1.jpg" alt="Photo de profil">
                    <div class="star mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg>
                    </div>
                        </div>

                        <div class="col-2">
                        <h5 class="fw-light"><u>Place restante</u></h5>
                        <p class="mt-5 ">1/4</p>
                        </div>

                        <div class="col-1">
                        <h5 class="fw-light"><u>Prix</u></h5>
                        <p class="mt-5 ">20</p>
                        </div>

                        <div class="col-3">
                        <h5 class="fw-light"><u>Jour | Heure | Durée</u></h5>
                        <p class="mt-5 ">17/03/25 | 10:30 | 1h30</p>
                        </div>

                        <div class="col-3">
                        <h5 class="fw-light"><u>Mention écologique *</u></h5>
                        <p class="mt-5 ">Oui</p>
                        </div>

                        <div class="d-flex col-1 align-items-center">
                    <button class="btn btn-primary mt-4">Détail</button>
                        </div>
                    

                    <div>
                        <p class="ast text-end">*Un voyage a la mention écologique s'il est effectué avec une voiture électrique.</p>
                    </div>
                    </div>
                </div>
            <!--Fin Profil-->

        </div>


    </div>
</section>

<script src="../../Controleur/covoit/covoit.js"></script>