<link rel="stylesheet" href="../../assets/styles/user/userHistory/userHistory.css">
   
<header>
<div class="hero-scene text-center text-black">
    <div class="hero-scene-content">
        <h1>Historique des trajets</h1>
    </div>
</div>
</header>


<section>
<div class="container mt-4">
        
        
        <!-- Onglets -->
        <ul class="nav nav-pills" id="trajetTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="en-cours-tab" data-bs-toggle="tab" data-bs-target="#en-cours" type="button" role="tab">Mes trajets en cours</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="futurs-tab" data-bs-toggle="tab" data-bs-target="#futurs" type="button" role="tab">Mes futurs trajets</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="historique-tab" data-bs-toggle="tab" data-bs-target="#historique" type="button" role="tab">Historique</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="trajetTabsContent">
            <!-- Onglet Mes trajets en cours -->
            <div class="tab-pane fade show active" id="en-cours" role="tabpanel">
                <h4>Mes trajets en cours</h4>
                <div class="alert alert-info">Aucun trajet en cours.</div>
            </div>

            <!-- Onglet Mes futurs trajets -->
            <div class="tab-pane fade" id="futurs" role="tabpanel">
                <h4>Proposer un trajet</h4>
                <form>
                    <div class="mb-3">
                        <label for="vehicule" class="form-label">Véhicule</label>
                        <select class="form-select" id="vehicule">
                            <option>Sélectionner un véhicule</option>
                            <option>Renault Clio - Essence</option>
                            <option>Tesla Model 3 - Électrique</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ville de départ</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ville d’arrivée</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jour et heure</label>
                        <input type="datetime-local" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durée estimée</label>
                        <input type="time" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-success">Proposer</button>
                </form>
            </div>

            <!-- Onglet Historique -->
            <div class="tab-pane fade" id="historique" role="tabpanel">
                <h4>Historique des trajets</h4>
                <div class="alert alert-info">Aucun trajet effectué.</div>
            </div>
        </div>
    </div>
</section>

<script src="../../Controleur/CRUD_trajets/get_trajets.js"> </script>