<?php
declare(strict_types=1);

namespace NetVOD\Classes;

class User
{
    protected string $email;

    protected string $id;
    protected array $listes = [];

    public function __construct(string $email, string $id)
    {
        $this->email = $email;
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
