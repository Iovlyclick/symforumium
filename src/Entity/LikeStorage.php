<?php

namespace App\Entity;

use App\Repository\LikeStorageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LikeStorageRepository::class)
 */
class LikeStorage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="likeStorages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity=Topic::class, inversedBy="likeStorages")
     */
    private $topicId;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="likeStorages")
     */
    private $postId;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="likeStorages")
     */
    private $commentId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $dislike;

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

    public function getDislike(): ?bool
    {
        return $this->dislike;
    }

    public function setDislike(?bool $dislike): self
    {
        $this->dislike = $dislike;

        return $this;
    }
}
