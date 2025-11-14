<?php
declare(strict_types=1);

namespace NetVOD\Classes;

class User
{
    protected string $email;
    protected int $id;
    protected string $nom;
    protected string $prenom;
    protected array $listes = [];

    public function __construct(string $email, int $id, string $nom = "", string $prenom = "")
    {
        $this->email = $email;
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
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
