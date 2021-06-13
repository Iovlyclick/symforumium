<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=Topic::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $topicId;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="integer")
     */
    private $likeAmount;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $reported;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="postId")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=LikeStorage::class, mappedBy="postId")
     */
    private $likeStorages;

    /**
     * @ORM\OneToMany(targetEntity=ReportStorage::class, mappedBy="postId")
     */
    private $reportStorages;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastEditedAt;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likeStorages = new ArrayCollection();
        $this->reportStorages = new ArrayCollection();
        $this->likeAmount = 0;
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

    public function getTopicId(): ?Topic
    {
        return $this->topicId;
    }

    public function setTopicId(?Topic $topicId): self
    {
        $this->topicId = $topicId;

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

    public function getLikeAmount(): ?int
    {
        return $this->likeAmount;
    }

    public function setLikeAmount(int $likeAmount): self
    {
        $this->likeAmount = $likeAmount;

        return $this;
    }

    public function addLike(): int
    {
        $this->setLikeAmount(++$this->likeAmount);
        return $this->likeAmount;
    }

    public function removeLike(): int
    {
        $this->setLikeAmount(--$this->likeAmount);

        return $this->likeAmount;
    }


    public function getReported(): ?bool
    {
        return $this->reported;
    }

    public function setReported(?bool $reported): self
    {
        $this->reported = $reported;

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
            $comment->setPostId($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPostId() === $this) {
                $comment->setPostId(null);
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
            $likeStorage->setPostId($this);
        }

        return $this;
    }

    public function removeLikeStorage(LikeStorage $likeStorage): self
    {
        if ($this->likeStorages->removeElement($likeStorage)) {
            // set the owning side to null (unless already changed)
            if ($likeStorage->getPostId() === $this) {
                $likeStorage->setPostId(null);
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
            $reportStorage->setPostId($this);
        }

        return $this;
    }

    public function removeReportStorage(ReportStorage $reportStorage): self
    {
        if ($this->reportStorages->removeElement($reportStorage)) {
            // set the owning side to null (unless already changed)
            if ($reportStorage->getPostId() === $this) {
                $reportStorage->setPostId(null);
            }
        }

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

    public function getLastEditedAt(): ?\DateTimeInterface
    {
        return $this->lastEditedAt;
    }

    public function setLastEditedAt(\DateTimeInterface $lastEditedAt): self
    {
        $this->lastEditedAt = $lastEditedAt;

        return $this;
    }
}
