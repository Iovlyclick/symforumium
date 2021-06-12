<?php

namespace App\Entity;

use App\Repository\ReportStorageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReportStorageRepository::class)
 */
class ReportStorage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reportStorages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity=Topic::class, inversedBy="reportStorages")
     */
    private $topicId;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="reportStorages")
     */
    private $postId;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="reportStorages")
     */
    private $commentId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

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

    public function getPostId(): ?Post
    {
        return $this->postId;
    }

    public function setPostId(?Post $postId): self
    {
        $this->postId = $postId;

        return $this;
    }

    public function getCommentId(): ?Comment
    {
        return $this->commentId;
    }

    public function setCommentId(?Comment $commentId): self
    {
        $this->commentId = $commentId;

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
}
