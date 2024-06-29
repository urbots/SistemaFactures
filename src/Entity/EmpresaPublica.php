<?php

namespace App\Entity;

use App\Repository\EmpresaPublicaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpresaPublicaRepository::class)]
class EmpresaPublica extends PersonaEmpresa
{
    #[ORM\OneToMany(targetEntity: Codis::class, mappedBy: 'empresaPublica')]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $codis;

    public function getId(): ?int
    {
        return $this->id;
    }
}
