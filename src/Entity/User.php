<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getUsers"])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(["getUsers"])]
    #[Assert\NotBlank(message: "You must enter a username")]
    #[Assert\Length(
        min: 1, 
        max: 180, 
        minMessage: "The username must be at least {{ limit }} character long", 
        maxMessage: "The username must be a maximum of {{ limit }} characters"
    )]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "You must enter a password")]
    #[Assert\PasswordStrength([
        'message' => 'Your password is too weak. Add numbers, upper, lower and special characters'
    ])]
    private ?string $password = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(["getUsers"])]
    #[assert\Length(
        min: 1,
        max: 50, 
        minMessage: "The name must be at least {{ limit }} character long", 
        maxMessage: "The name must be a maximum of {{ limit }} characters"
    )]
    #[Assert\NotBlank([
        'message' => 'You must enter a name'
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(["getUsers"])]
    #[assert\Length(
        min: 1,
        max: 50, 
        minMessage: "The surname must be at least {{ limit }} character long", 
        maxMessage: "The surname must be a maximum of {{ limit }} characters"
    )]
    #[Assert\NotBlank([
        'message' => 'You must enter a surname'
    ])]
    private ?string $surname = null;

    #[ORM\Column(length: 50)]
    #[Groups(["getUsers"])]
    #[assert\Length(
        max: 50, 
        maxMessage : "The email must be a maximum of {{ limit }} characters"
    )]
    #[Assert\NotBlank([
        'message' => 'You must enter an email adress'
    ])]
    #[Assert\Email([
        'message' => 'Incorrect email address'
    ])]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    #[Groups(["getUsers"])]
    private ?\DateTimeInterface $creation_date = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank([
        'message' => 'You must enter a client id'
    ])]
    #[Groups(["getUsers"])]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): static
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreationDateValue()
    {
        $this->creation_date = new \DateTimeImmutable();
    }
    
    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
    }
}
