<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Movie", inversedBy="movieUsers")
     * @ORM\JoinTable(name="user_movies")
     */
    private $userMovies;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    public function __construct()
    {
        $this->userMovies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getUserMovies(): Collection
    {
        return $this->userMovies;
    }

    /**
     * @return integer
     */
    public function getUserMoviesCount()
    {
        return $this->userMovies->count();
    }

    /**
     * @return string
     */
    public function getUserMoviesString()
    {
        $movies = "";
        $allMovies = $this->getUserMovies();
        if (isset($allMovies[0])) {
            foreach ($allMovies as $item) {
                $movies = $movies . $item->getName() . '; ';
            }
            $movies = substr($movies, 0, -2);
        }
        return $movies;
    }

    public function addUserMovie(Movie $userMovie): self
    {
        if (!$this->userMovies->contains($userMovie)) {
            $this->userMovies[] = $userMovie;
        }

        return $this;
    }

    public function removeUserMovie(Movie $userMovie): self
    {
        if ($this->userMovies->contains($userMovie)) {
            $this->userMovies->removeElement($userMovie);
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
