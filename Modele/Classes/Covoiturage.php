<?php
class Covoiturage
{
    public $id;
    public $depart;
    public $arrivee;
    public $jour;
    public $heure;
    public $duree;
    public $nb_places_restantes;
    public $prix;
    public $etat;
    public $energie;

    public function __construct($row)
    {
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

    public function mentionEcologique()
    {
        return strtolower((string) $this->energie) === 'electrique' ? 'Oui' : 'Non';
    }

    public function toArray()
    {
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
            'mention_ecologique' => $this->mentionEcologique(),
        ];
    }
}
