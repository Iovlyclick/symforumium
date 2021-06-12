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
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Topic::class, mappedBy="author")
     */
    private $topics;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="author", cascade={"persist"})
     * 
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author", cascade={"persist"}))
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=LikeStorage::class, mappedBy="userId", orphanRemoval=true)
     */
    private $likeStorages;

    /**
     * @ORM\OneToMany(targetEntity=ReportStorage::class, mappedBy="userId", orphanRemoval=true)
     */
    private $reportStorages;


    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likeStorages = new ArrayCollection();
        $this->reportStorages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

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
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

        /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * @return Collection|Topic[]
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->setAuthor($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getAuthor() === $this) {
                $topic->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LikeStorage[]
     */
    public function getLikeStorages(): Collection
    {
        return $this->likeStorages;
    }

    public function addLikeStorage(LikeStorage $likeStorage): self
    {
        if (!$this->likeStorages->contains($likeStorage)) {
            $this->likeStorages[] = $likeStorage;
            $likeStorage->setUserId($this);
        }

        return $this;
    }

    public function removeLikeStorage(LikeStorage $likeStorage): self
    {
        if ($this->likeStorages->removeElement($likeStorage)) {
            // set the owning side to null (unless already changed)
            if ($likeStorage->getUserId() === $this) {
                $likeStorage->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReportStorage[]
     */
    public function getReportStorages(): Collection
    {
        return $this->reportStorages;
    }

    public function addReportStorage(ReportStorage $reportStorage): self
    {
        if (!$this->reportStorages->contains($reportStorage)) {
            $this->reportStorages[] = $reportStorage;
            $reportStorage->setUserId($this);
        }

        return $this;
    }

    public function removeReportStorage(ReportStorage $reportStorage): self
    {
        if ($this->reportStorages->removeElement($reportStorage)) {
            // set the owning side to null (unless already changed)
            if ($reportStorage->getUserId() === $this) {
                $reportStorage->setUserId(null);
            }
        }

        return $this;
    }
}