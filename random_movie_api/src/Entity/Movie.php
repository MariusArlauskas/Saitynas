<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 */
class Movie
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
     * @ORM\Column(type="string", length=255)
     */
    private $author;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $release_date;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="userMovies")
     * @ORM\JoinTable(name="movie_users")
     */
    private $movieUsers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Genre", mappedBy="genreMovies")
     * @ORM\JoinTable(name="movie_genres")
     */
    private $movieGenres;

    public function __construct()
    {
        $this->movieUsers = new ArrayCollection();
        $this->movieGenres = new ArrayCollection();
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

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->release_date;
    }

    public function setReleaseDate(\DateTime $release_date): self
    {
        $this->release_date = $release_date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMovieUsers(): Collection
    {
        return $this->movieUsers;
    }

    /**
     * @return integer
     */
    public function getMovieUsersCount()
    {
        return $this->movieUsers->count();
    }

    public function addMovieUser(User $movieUser): self
    {
        if (!$this->movieUsers->contains($movieUser)) {
            $this->movieUsers[] = $movieUser;
            $movieUser->addUserMovie($this);
        }

        return $this;
    }

    public function removeMovieUser(User $movieUser): self
    {
        if ($this->movieUsers->contains($movieUser)) {
            $this->movieUsers->removeElement($movieUser);
            $movieUser->removeUserMovie($this);
        }

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getMovieGenres(): Collection
    {
        return $this->movieGenres;
    }

    /**
     * @return String
     */
    public function getMovieGenresString()
    {
        $genres = "";
        $allGenres = $this->getMovieGenres();
        if (isset($allGenres[0])) {
            foreach ($allGenres as $genre) {
                $genres = $genres . $genre->getName() . ', ';
            }
            $genres = substr($genres, 0, -2);
        }
        return $genres;
    }

    public function addMovieGenre(Genre $movieGenre): self
    {
        if (!$this->movieGenres->contains($movieGenre)) {
            $this->movieGenres[] = $movieGenre;
            $movieGenre->addGenreMovie($this);
        }

        return $this;
    }

    public function removeMovieGenre(Genre $movieGenre): self
    {
        if ($this->movieGenres->contains($movieGenre)) {
            $this->movieGenres->removeElement($movieGenre);
            $movieGenre->removeGenreMovie($this);
        }

        return $this;
    }
}
