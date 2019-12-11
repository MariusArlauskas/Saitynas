<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GenreRepository")
 */
class Genre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Movie", inversedBy="movieGenres")
     * @ORM\JoinTable(name="genre_movies")
     */
    private $genreMovies;

    public function __construct()
    {
        $this->genreMovies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getGenreMovies(): Collection
    {
        return $this->genreMovies;
    }

    /**
     * @return integer
     */
    public function getGenreMoviesCount()
    {
        return $this->genreMovies->count();
    }

    public function addGenreMovie(Movie $genreMovie): self
    {
        if (!$this->genreMovies->contains($genreMovie)) {
            $this->genreMovies[] = $genreMovie;
        }

        return $this;
    }

    public function removeGenreMovie(Movie $genreMovie): self
    {
        if ($this->genreMovies->contains($genreMovie)) {
            $this->genreMovies->removeElement($genreMovie);
        }

        return $this;
    }
}
