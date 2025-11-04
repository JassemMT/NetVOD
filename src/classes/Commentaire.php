<?php
declare(strict_types=1);

namespace NetVOD\Classes;

class Commentaire
{
    protected int $id;
    protected int $note; // 1..5
    protected string $contenu;
    protected \DateTime $date;

    public function __construct(int $id, int $note, string $contenu, string $date = null)
    {
        $this->note = $note;
        $this->contenu = $contenu;
        $this->date = $date ?? new \DateTime();
    }

    public function __get(string $attr)
    {
        return $this->$attr ?? null;
    }

    public function __set(string $attr, $value): void
    {
        $this->$attr = $value;
    }
}
