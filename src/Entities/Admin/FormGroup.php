<?php
namespace App\Entities\Admin;

class FormGroup
{
  private $id;
  private $name;
  private $uri;
  private $isactive;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(?int $id): void
  {
    $this->id = $id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(?string $name): void
  {
    $this->name = $name;
  }

  public function getUri(): ?string
  {
    return $this->uri;
  }

  public function setUri(?string $uri): void
  {
    $this->uri = $uri;
  }

    public function getIsActive(): ?bool
  {
    return $this->isactive;
  }

  public function setIsActive(?bool $isactive): void
  {
    $this->isactive = $isactive;
  }
}
