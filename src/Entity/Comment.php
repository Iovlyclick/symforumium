<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $editedAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $likeAmount;

    /**
     * @ORM\Column(type="integer")
     */
    private $postId;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="likedComment")
     */
    private $userThatLiked;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $reportedBy;

    public function __construct()
    {
        $this->userThatLiked = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getEditedAt(): ?\DateTimeInterface
    {
        return $this->editedAt;
    }

    public function setEditedAt(\DateTimeInterface $editedAt): self
    {
        $this->editedAt = $editedAt;

        return $this;
    }

    public function getLikeAmount(): ?int
    {
        return $this->likeAmount;
    }

    public function setLikeAmount(int $likeAmount): self
    {
        $this->likeAmount = $likeAmount;

        return $this;
    }

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): self
    {
        $this->postId = $postId;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserThatLiked(): Collection
    {
        return $this->userThatLiked;
    }

    public function addUserThatLiked(User $userThatLiked): self
    {
        if (!$this->userThatLiked->contains($userThatLiked)) {
            $this->userThatLiked[] = $userThatLiked;
            $userThatLiked->addLikedComment($this);
        }

        return $this;
    }

    public function removeUserThatLiked(User $userThatLiked): self
    {
        if ($this->userThatLiked->removeElement($userThatLiked)) {
            $userThatLiked->removeLikedComment($this);
        }

        return $this;
    }

    public function getReportedBy(): ?User
    {
        return $this->reportedBy;
    }

    public function setReportedBy(?User $reportedBy): self
    {
        $this->reportedBy = $reportedBy;

        return $this;
    }
}
