<link rel="stylesheet" href="../../assets/styles/user/userSpace/userSpace.css">


<header>
    <div class="hero-scene text-center text-black">
        <div class="hero-scene-content">
            <h1>Votre espace</h1>
        </div>
    </div>
</header>

<section>
<div class="container mt-4">
    <!-- Crédit de l'utilisateur -->
    <div class="d-flex justify-content-end">
        <h5>Crédits : <span class="badge bg-success" id="user-credits">0</span></h5>
    </div>

    <!-- Photo de profil -->
    <div class="text-center mb-4">
    <img id="profil-photo" src="../../uploads/photos_utilisateurs/default.jpg" alt="Photo de profil" style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%;">
    <div class="mt-2">
        <input type="file" id="photo-input" accept="image/*" class="form-control form-control-sm" style="max-width: 300px; margin: auto;">
        <button class="btn btn-primary btn-sm mt-2" onclick="uploadPhoto()">Changer la photo</button>
    </div>
</div>


    <!-- Informations personnelles -->
    <div class="card p-3 mt-3">
        <h4>Informations personnelles</h4>
        <div class="row">
            <div class="col-md-6">Prénom actuel : <strong id="user-firstname"></strong></div>
            <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editField('prenom')">Modifier</button></div>
        </div>
       
        <div class="row mt-2">
            <div class="col-md-6">Email actuel : <strong id="user-email"></strong></div>
            <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editField('email')">Modifier</button></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">Mot de passe : <strong>********</strong></div>
            <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editPassword()">Modifier</button></div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">Type de compte : <strong id="user-role"></strong></div>
            <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editField('role')">Modifier</button></div>
        </div>
    </div>

    <!-- Modale pour changer de rôle -->
<div id="role-modal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.6); z-index:9999;">
  <div style="background:white; padding:20px; margin:10% auto; width:300px; border-radius:8px;">
    <h5>Choisir un rôle</h5>
    <select id="role-select" class="form-select mt-2">
      <option value="passager">Passager</option>
      <option value="chauffeur">Chauffeur</option>
      <option value="passager-chauffeur">Passager-Chauffeur</option>
    </select>
    <div class="d-flex justify-content-end mt-3">
      <button class="btn btn-secondary me-2" onclick="closeRoleModal()">Annuler</button>
      <button class="btn btn-success" onclick="submitRoleChange()">Valider</button>
    </div>
  </div>
</div>


    <!-- Informations véhicule -->
     <!-- Immatriculation -->
    <div class="card p-3 mt-3" id="vehicle-card" style="display: block;">
        <h4>Informations véhicule</h4>
        <div class="row mt-2">
            <div class="col-md-6">Plaque d'immatriculation : <strong id="vehicle-plate"></strong></div>
            <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editVehicle('immatriculation')">Modifier</button></div>
        </div>
         <!-- Date première immatriculation -->
        <div class="row mt-2">
             <div class="col-md-6">Date de première immatriculation : <strong id="vehicle-date"></strong></div>
             <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editVehicle('date_immatriculation')">Modifier</button></div>
        </div>
        <!-- Modèle -->
        <div class="row mt-2">
            <div class="col-md-6">Modèle : <strong id="vehicle-model"></strong></div>
            <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editVehicle('modele')">Modifier</button></div>
        </div>
        <!-- couleur -->
        <div class="row mt-2">
            <div class="col-md-6">Couleur : <strong id="vehicle-color"></strong></div>
            <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editVehicle('couleur')">Modifier</button></div>
        </div>
        <!-- Marque -->
        <div class="row mt-2">
            <div class="col-md-6">Marque : <strong id="vehicle-brand"></strong></div>
            <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editVehicle('marque')">Modifier</button></div>
        </div>
        <!-- Nombre de places -->
        <div class="row mt-2">
            <div class="col-md-6">Nombre de places : <strong id="vehicle-seats"></strong></div>
            <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editVehicle('nb_places')">Modifier</button></div>
        </div>
        <!-- Energie -->
        <div class="row mt-2">
            <div class="col-md-6">Énergie : <strong id="vehicle-energy"></strong></div>
            <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editVehicle('energie')">Modifier</button></div>
        </div>
       
        <!-- Préférences -->
         <!-- Fumeur -->
        <div class="row mt-2">
             <div class="col-md-6">Fumeur : <strong id="vehicle-pref-fumeur"></strong></div>
             <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editPreference('fumeur')">Modifier</button></div>
        </div>
        <!-- Animaux -->
        <div class="row mt-2">
              <div class="col-md-6">Animaux : <strong id="vehicle-pref-animaux"></strong></div>
              <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editPreference('animaux')">Modifier</button></div>
        </div>
        <!-- Discussion -->
        <div class="row mt-2">
              <div class="col-md-6">Discussions : <strong id="vehicle-pref-discussions"></strong></div>
              <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editPreference('discussions')">Modifier</button></div>
        </div>
        <!-- Musique -->
        <div class="row mt-2">
              <div class="col-md-6">Musique : <strong id="vehicle-pref-musique"></strong></div>
              <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editPreference('musique')">Modifier</button></div>
        </div>
        <!-- Autre -->
        <div class="row mt-2">
              <div class="col-md-6">Autre préférence : <strong id="vehicle-pref-autre"></strong></div>
              <div class="col-md-6 text-end"><button class="btn btn-success btn-sm" onclick="editPreference('autre')">Modifier</button></div>
        </div>

        <!-- Boutons -->
        <div class="row mt-3">
            <div class="col-md-12 text-end">
                <button class="btn btn-danger btn-sm" onclick="deleteVehicle()">Supprimer ce véhicule</button>
                <button class="btn btn-primary btn-sm" onclick="addVehicle()">Ajouter un véhicule</button>
            </div>
            
        </div>
    </div>
    <!-- Bouton Vehicule suivant -->
    <div class="text-end mt-2">
        <button id="vehicule-next-btn" class="btn btn-star btn-sm" style="display: none;">Suivant</button>
    </div>

    <!-- Bouton Voir mes trajets -->
    <div class="d-flex justify-content-center mt-4">
        <a class="btn btn-success" href="/Trajets" role="button">Voir mes trajets</a>
    </div>
</div>
</section>
