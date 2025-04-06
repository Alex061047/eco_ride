<link rel="stylesheet" href="../../assets/styles/app.css">

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4 border-0" style="max-width: 400px; width: 100%; border-radius: 20px;">
        <h3 id="form-title" class="text-center mb-4">Connexion</h3>
        <form id="form">
            <div id="pseudo-field" class="mb-3" style="display: none;">
                <input type="text" name="pseudo" class="form-control" placeholder="Pseudo">
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Adresse e-mail" required>
            </div>
            <div class="mb-3">
                <input type="password" name="mot_de_passe" class="form-control" placeholder="Mot de passe" required>
            </div>
            <button type="submit" id="submit-button" class="btn btn-success w-100">Se connecter</button>
        </form>
        <div class="text-center mt-3">
            <button id="toggle-button" class="btn btn-primary">Pas encore de compte ? Inscrivez-vous</button>
        </div>
        <div id="message" class="text-center mt-3"></div>
    </div>
</div>
