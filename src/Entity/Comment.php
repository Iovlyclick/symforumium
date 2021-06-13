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
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $postId;

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
     * @ORM\OneToMany(targetEntity=LikeStorage::class, mappedBy="commentId")
     */
    private $likeStorages;

    /**
     * @ORM\OneToMany(targetEntity=ReportStorage::class, mappedBy="commentId")
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

    public function getPostId(): ?Post
    {
        return $this->postId;
    }

    public function setPostId(?Post $postId): self
    {
        $this->postId = $postId;

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
            $likeStorage->setCommentId($this);
        }

        return $this;
    }

    public function removeLikeStorage(LikeStorage $likeStorage): self
    {
        if ($this->likeStorages->removeElement($likeStorage)) {
            // set the owning side to null (unless already changed)
            if ($likeStorage->getCommentId() === $this) {
                $likeStorage->setCommentId(null);
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
            $reportStorage->setCommentId($this);
        }

        return $this;
    }

    public function removeReportStorage(ReportStorage $reportStorage): self
    {
        if ($this->reportStorages->removeElement($reportStorage)) {
            // set the owning side to null (unless already changed)
            if ($reportStorage->getCommentId() === $this) {
                $reportStorage->setCommentId(null);
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
