<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 */
class Comment
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $comment;


    /**
     * @var Cinema
     *
     * @ORM\ManyToOne(targetEntity=Cinema::class, inversedBy="comments")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $cinema;
    /**
     * @var Books
     *
     * @ORM\ManyToOne(targetEntity=Books::class, inversedBy="comments")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $books;
    /**
     * @var Games
     *
     * @ORM\ManyToOne(targetEntity=Games::class, inversedBy="comments")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $games;


    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(referencedColumnName="id")
     */

    private $user;


    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="date_immutable")
     */
    private $createdAt;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="date_immutable")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @param string $content
     * @param User $user
     * @param Post $post
     *
     * @return Comment
     */
    public static function create(string $content, User $user, Post $post): Comment
    {
        $comment = new self();
        $comment->comment = $content;
        $comment->post = $post;
        $comment->user = $user;

        return $comment;
    }

    /**
     * @return ?string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return ?User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post)
    {
        $this->post = $post;
    }
    /**
     * @return Cinema
     */
    public function getCinema(): Cinema
    {
        return $this->cinema;
    }
    /**
     * @param Cinema $cinema
     */
    public function setCinema(Cinema $cinema)
    {
        $this->cinema = $cinema;
    }
    /**
     * @return Books
     */
    public function getBooks(): Books
    {
        return $this->books;
    }
    /**
     * @param Books $books
     */
    public function setBooks(Books $books)
    {
        $this->books = $books;
    }
    /**
     * @return Games
     */
    public function getGames(): Games
    {
        return $this->games;
    }
    /**
     * @param Games $games
     */
    public function setGames(Games $games)
    {
        $this->games = $games;
    }

    /**
     * @param ?User $user
     */
    public function setUser(?User $user)
    {
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}