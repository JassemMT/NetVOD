<?php
declare(strict_types=1);

namespace NetVOD\Classes;

class User
{
    protected string $email;
    protected string $mdp_hash;
  
    protected array $listes = [];

    public function __construct(string $email, string $mdp_hash)
    {
        $this->email = $email;
        $this->mdp_hash = $mdp_hash;
        $this->id_user = $id;
    }

    public function __get(string $attr)
    {
        return $this->$attr ?? null;
    }

    public function __set(string $attr, $value): void
    {
        $this->$attr = $value;
    }

    public function ajouterListe(ListeProgramme $l): void
    {
        $this->listes[] = $l;
    }

    public function getListe(string $nom): ?ListeProgramme
    {
        foreach ($this->listes as $liste) {
            if ($liste->nom === $nom) {
                return $liste;
            }
        }
        return null;
    }
}
