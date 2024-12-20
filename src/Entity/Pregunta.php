<?php

namespace App\Entity;

use App\Repository\PreguntaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PreguntaRepository::class)]
class Pregunta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    public ?string $titulo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    public ?\DateTimeInterface $fecha_inicio = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    public ?\DateTimeInterface $fecha_fin = null;

    /**
     * @var Collection<int, Respuesta>
     */
    #[ORM\OneToMany(targetEntity: Respuesta::class, mappedBy: 'pregunta', orphanRemoval: true)]
    public Collection $respuesta;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $respuesta1 = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $respuesta2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $respuesta3 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $respuesta4 = null;

    public function __construct()
    {
        $this->respuesta = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getFechaInicio(): ?\DateTimeInterface
    {
        return $this->fecha_inicio;
    }

    public function setFechaInicio(\DateTimeInterface $fecha_inicio): static
    {
        $this->fecha_inicio = $fecha_inicio;

        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fecha_fin;
    }

    public function setFechaFin(?\DateTimeInterface $fecha_fin): static
    {
        $this->fecha_fin = $fecha_fin;

        return $this;
    }

    /**
     * @return Collection<int, Respuesta>
     */
    public function getRespuesta(): Collection
    {
        return $this->respuesta;
    }

    public function addRespuestum(Respuesta $respuestum): static
    {
        if (!$this->respuesta->contains($respuestum)) {
            $this->respuesta->add($respuestum);
            $respuestum->setPregunta($this);
        }

        return $this;
    }

    public function removeRespuestum(Respuesta $respuestum): static
    {
        if ($this->respuesta->removeElement($respuestum)) {
            // set the owning side to null (unless already changed)
            if ($respuestum->getPregunta() === $this) {
                $respuestum->setPregunta(null);
            }
        }

        return $this;
    }

    public function getRespuesta1(): ?string
    {
        return $this->respuesta1;
    }

    public function setRespuesta1(string $respuesta1): static
    {
        $this->respuesta1 = $respuesta1;

        return $this;
    }

    public function getRespuesta2(): ?string
    {
        return $this->respuesta2;
    }

    public function setRespuesta2(string $respuesta2): static
    {
        $this->respuesta2 = $respuesta2;

        return $this;
    }

    public function getRespuesta3(): ?string
    {
        return $this->respuesta3;
    }

    public function setRespuesta3(?string $respuesta3): static
    {
        $this->respuesta3 = $respuesta3;

        return $this;
    }

    public function getRespuesta4(): ?string
    {
        return $this->respuesta4;
    }

    public function setRespuesta4(?string $respuesta4): static
    {
        $this->respuesta4 = $respuesta4;

        return $this;
    }
}
