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
  <form class="row justify-content-center mt-3" id="form-recherche">
    <div class="col-sm-1"></div>

    <div class="col-sm-2">
      <input type="text" class="form-control" id="jour" placeholder="Jour" 
        onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'">
    </div>

    <div class="col-sm-2">
      <input type="text" class="form-control" id="depart" placeholder="Ville de départ">
    </div>

    <div class="col-sm-2">
      <input type="text" class="form-control" id="arrivee" placeholder="Ville d'arrivée">
    </div>

    <div class="col-sm-auto">
      <button type="submit" class="btn btn-success w-100">Rechercher</button>
    </div>
  </form>
</section>

<section>
  <div class="container mt-4">

    <!-- Filtres -->
    <div class="d-flex align-items-center gap-3 filtre flex-wrap">
      <div><h5 class="fw-light"><u>Filtres :</u></h5></div>

      <div class="d-flex align-items-center">
        <label for="mention" class="me-1">Mention écologique</label>
        <input type="checkbox" id="mention" />
      </div>

      <div class="d-flex align-items-center">
        <label for="prix" class="me-1">Prix maximum</label>
        <input type="number" id="prix" class="form-control" style="width: 80px;" />
      </div>

      <div class="d-flex align-items-center">
        <label for="duree" class="me-1">Durée maximum</label>
        <input type="time" id="duree" class="form-control" style="width: 120px;" />
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
    <div class="row mt-2 justify-content-center" id="liste-covoiturages">
      <!-- Le contenu est ajouté dynamiquement par covoit.js -->
    </div>

  </div>
</section>
