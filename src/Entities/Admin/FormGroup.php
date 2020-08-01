<?php
namespace App\Entities\Admin;

class FormGroup
{
  private $id;
  private $name;
  private $base_uri;
  private $is_active;

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

  public function getBaseUri(): ?string
  {
    return $this->base_uri;
  }

  public function setBaseUri(?string $base_uri): void
  {
    $this->base_uri = $base_uri;
  }

    public function getIsActive(): ?bool
  {
    return $this->is_active;
  }

  public function setIsActive(?bool $is_active): void
  {
    $this->is_active = $is_active;
  }
}
