<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentaireRepository::class)
 */
class Commentaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Publication::class, inversedBy="commentaires")
     */
    private $pub;

    /**
     * @ORM\Column(type="string", length=255)
     * * @Assert\NotBlank(message="Le Champ nom est obligatoire")
     * @Assert\Length(
     *     min=4,
     *     max=50,
     *     minMessage="Le titre doit contenir au moins 4 carcatères ",
     *     maxMessage="Le titre doit contenir au plus 20 carcatères"
     * )
     */
    private $TextCommentaire;

    /**
     * @ORM\Column(type="datetime")
     */
    private $DateCommentaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imageCommentaire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPub(): ?Publication
    {
        return $this->pub;
    }

    public function setPub(?Publication $pub): self
    {
        $this->pub = $pub;

        return $this;
    }
    public function __toString(): string
    {
        return (string) $this->pub;
    }

    public function getTextCommentaire(): ?string
    {
        return $this->TextCommentaire;
    }

    public function setTextCommentaire(string $TextCommentaire): self
    {
        $this->TextCommentaire = $TextCommentaire;

        return $this;
    }

    public function getDateCommentaire(): ?\DateTimeInterface
    {
        return $this->DateCommentaire;
    }

    public function setDateCommentaire(\DateTimeInterface $DateCommentaire): self
    {
        $this->DateCommentaire = $DateCommentaire;

        return $this;
    }

    public function getImageCommentaire(): ?string
    {
        return $this->imageCommentaire;
    }

    public function setImageCommentaire(string $imageCommentaire): self
    {
        $this->imageCommentaire = $imageCommentaire;

        return $this;
    }
}
