<?php
declare(strict_types=1);

namespace NetVOD\Classes;

class ListeProgramme
{
    protected string $nom;
    /** @var Programme[] */
    protected array $programmes = [];

    public function __construct(string $nom)
    {
        $this->nom = $nom;
    }

    public function __get(string $attr)
    {
        return $this->$attr ?? null;
    }

    public function __set(string $attr, $value): void
    {
        $this->$attr = $value;
    }

    public function ajouterProgramme(Programme $p): void
    {
        $this->programmes[] = $p;
    }

    public function retirerProgramme(Programme $p): void
    {
        foreach ($this->programmes as $k => $prog) {
            if ($prog === $p) {
                unset($this->programmes[$k]);
                $this->programmes = array_values($this->programmes);
                return;
            }
        }
    }

    /**
     * @return Programme[]
     */
    public function getProgrammes(): array
    {
        return $this->programmes;
    }
}
