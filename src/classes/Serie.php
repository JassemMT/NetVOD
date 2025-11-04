<?php
declare(strict_types=1);

namespace netvod\classes;
use netvod\classes\Episode;



class Serie implements Programme {

    private string $titre;
    private string $description;
    private int $annee;
    private string $image;
    private array $episodes = [];

    public function __construct(string $titre, string $description, int $annee, string $image) {
        $this->titre = $titre;
        $this->description = $description;
        $this->annee = $annee;
        $this->image = $image;
    }

    public function __get(string $attr) {
        return $this->$attr ?? null;
    }

    public function __set(string $attr, mixed $value) {
        $this->$attr = $value;
    }

    public function getTitre() : string {
        return $this->titre;
    }

    public function getSynopsis() : string {
        return $this->description;
    }

    public function getImage() : string {
        return $this->image;
    }

    public function ajouterEpisode(Episode $episode) {
        $this->episodes[] = $episode;
    }

    public function getEpisodes() : array {
        return $this->episodes;
    }

    public function noteMoyenne() : float {
        if(empty($this->episodes)) {
            return 0.0;
        }

        $total = 0;
        $count = 0;

        foreach($this->episodes as $episode) {
            $total += $episode->getNote();
                $count++;
        }

        return $total / $count;
    }
}
