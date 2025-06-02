<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Please enter your comment')]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $insertDate = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?News $news = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getInsertDate(): ?\DateTimeImmutable
    {
        return $this->insertDate;
    }

    public function setInsertDate(\DateTimeImmutable $insertDate): static
    {
        $this->insertDate = $insertDate;

        return $this;
    }

    #[ORM\PrePersist]
    public function setInsertDateValue(): void
    {
        $this->insertDate = new \DateTimeImmutable();
    }

    public function getNews(): ?News
    {
        return $this->news;
    }

    public function setNews(?News $news): static
    {
        $this->news = $news;

        return $this;
    }
}
