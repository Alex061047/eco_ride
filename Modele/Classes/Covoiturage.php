<?php
// Déclaration de la classe Covoiturage représentant un trajet
class Covoiturage {
    // Déclaration des propriétés publiques de la classe
    public $id, $depart, $arrivee, $jour, $heure, $duree, $nb_places_restantes, $prix, $etat, $energie;

    // Constructeur
    public function __construct($row) {
        $this->id = $row['id'];
        $this->depart = $row['depart'];
        $this->arrivee = $row['arrivee'];
        $this->jour = $row['jour'];
        $this->heure = $row['heure'];
        $this->duree = $row['duree'];
        $this->nb_places_restantes = $row['nb_places_restantes'];
        $this->prix = $row['prix'];
        $this->etat = $row['etat'];
        $this->energie = $row['energie'];
    }

    // Méthode pour déterminer si le covoiturage est écologique
    public function mentionEcologique() {
        return strtolower($this->energie) === 'electrique' || strtolower($this->energie) === 'électrique' ? 'Oui' : 'Non';
    }

    // Méthode pour convertir l’objet en tableau associatif et être encodé en JSON
    public function toArray() {
        return [
            'id' => $this->id,
            'depart' => $this->depart,
            'arrivee' => $this->arrivee,
            'jour' => $this->jour,
            'heure' => $this->heure,
            'duree' => $this->duree,
            'nb_places_restantes' => $this->nb_places_restantes,
            'prix' => $this->prix,
            'etat' => $this->etat,
            'mention_ecologique' => $this->mentionEcologique()
        ];
    }
}
