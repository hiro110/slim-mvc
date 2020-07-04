<?php
namespace App\entities;

class User
{
  private $id;
  private $username;
  private $password;
  private $role;
  private $is_active;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(?int $id): void
  {
    $this->id = $id;
  }

  public function getUsername(): ?string
  {
    return $this->username;
  }

  public function setUsername(?string $username): void
  {
    $this->username = $username;
  }

  public function getPassword(): ?string
  {
    return $this->password;
  }

  public function setPassword(?string $password): void
  {
    $this->password = $password;
  }

  public function getRole(): ?int
  {
    return $this->role;
  }

  public function setRole(?int $role): void
  {
    $this->role = $role;
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
