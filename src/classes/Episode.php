<?php
declare(strict_types=1);

namespace netvod\classes;

class Episode
{
    protected int $id;
    protected int $numero;
    protected string $titre;
    protected string $description;
    protected int $duree; // en secondes
    protected string $source; // url ou chemin
    protected string $image;

<<<<<<< HEAD
    public function __construct(int $numero, string $titre, string $description, int $duree, string $source, string $image)
=======
    public function __construct(int $id, int $numero , string $titre, string $description, int $duree, string $source, string $image)
>>>>>>> 3026385b7c0e1ee01e7e7836418fc1a87ac33330
    {
        $this->id = $id;
        $this->numero = $numero;
        $this->titre = $titre;
        $this->description = $description;
        $this->duree = $duree;
        $this->source = $source;
        $this->image = $image;
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
