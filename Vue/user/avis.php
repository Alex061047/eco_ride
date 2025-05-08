<link rel="stylesheet" href="../../assets/styles/user/avis/avis.css">

<section>
<div class="container avis-box">
    <h2 class="text-center">Comment s'est passé votre trajet ?</h2>
    <form id="form-avis">

    <!-- Notation sous forme d'étoiles -->
         <div class="mb-3 text-center">
         <label class="form-label" for="note">Votre note :</label><br>
             <div id="etoile"></div>
             <!-- Champ caché pour stocker la note sélectionnée -->
             <input type="hidden" name="note" id="note" value="0">
        </div>


        <!-- Champ commentaire -->
        <div class="mb-3">
            <label for="commentaire" class="form-label">Votre avis :</label>
            <textarea class="form-control" id="commentaire" name="commentaire" rows="4" placeholder="Dites-nous ce que vous avez pensé du trajet..."></textarea>
        </div>

         <!-- Champs cachés pour envoyer les identifiants nécessaires à l'enregistrement de l'avis -->
        <input type="hidden" name="user_id" id="user_id">
        <input type="hidden" name="trajet_id" id="trajet_id">
        <input type="hidden" name="token" id="token">


        <!-- Bouton du formulaire -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Envoyer mon avis</button>
        </div>
    </form>

    <!-- Affichage succès ou d'echec d'envoi -->
    <div id="resultat" class="mt-3 text-center"></div>
</div>
    </section>