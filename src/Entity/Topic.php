<?php

namespace App\Entity;

use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TopicRepository::class)
 */
class Topic
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="topics")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

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
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="topicId")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=LikeStorage::class, mappedBy="topicId")
     */
    private $likeStorages;

    /**
     * @ORM\OneToMany(targetEntity=ReportStorage::class, mappedBy="topicId")
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

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archived;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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
            $post->setTopicId($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getTopicId() === $this) {
                $post->setTopicId(null);
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
            $likeStorage->setTopicId($this);
        }

        return $this;
    }

    public function removeLikeStorage(LikeStorage $likeStorage): self
    {
        if ($this->likeStorages->removeElement($likeStorage)) {
            // set the owning side to null (unless already changed)
            if ($likeStorage->getTopicId() === $this) {
                $likeStorage->setTopicId(null);
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
            $reportStorage->setTopicId($this);
        }

        return $this;
    }

    public function removeReportStorage(ReportStorage $reportStorage): self
    {
        if ($this->reportStorages->removeElement($reportStorage)) {
            // set the owning side to null (unless already changed)
            if ($reportStorage->getTopicId() === $this) {
                $reportStorage->setTopicId(null);
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

    public function getArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(?bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }
}
