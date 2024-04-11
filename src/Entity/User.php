<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * @Hateoas\Relation(
 *      "self", 
 *      href= @Hateoas\Route(
 *          "getUser", 
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getUsers")
 * )
 * 
 * @Hateoas\Relation(
 *      "delete", 
 *      href = @Hateoas\Route(
 *          "deleteUser", 
 *          parameters = { "id" = "expr(object.getId())" }
 *      ), 
 *      exclusion = @Hateoas\Exclusion(groups="getUsers")
 * )
 * 
 * @Hateoas\Relation(
 *      "post", 
 *      href = @Hateoas\Route(
 *          "addUser"
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getUsers")
 * )
 * 
 * @Hateoas\Relation(
 *      "get", 
 *      href= @Hateoas\Route(
 *          "getUsersByClient"
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getUsers")
 * )
 * 
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[ORM\HasLifecycleCallbacks]
class User implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getUsers"])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(["getUsers", "createUser"])]
    #[Assert\NotBlank(message: "You must enter a username")]
    #[Assert\Length(
        min: 1, 
        max: 180, 
        minMessage: "The username must be at least {{ limit }} character long", 
        maxMessage: "The username must be a maximum of {{ limit }} characters"
    )]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Groups(["createUser"])]
    #[Assert\NotBlank(message: "You must enter a password")]
    #[Assert\PasswordStrength([
        'message' => 'Your password is too weak. Add numbers, upper, lower and special characters'
    ])]
    private ?string $password = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(["getUsers", "createUser"])]
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
    #[Groups(["getUsers", "createUser"])]
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
    #[Groups(["getUsers", "createUser"])]
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
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
        $this->creation_date = \DateTime::createFromImmutable(new \DateTimeImmutable());
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
}
