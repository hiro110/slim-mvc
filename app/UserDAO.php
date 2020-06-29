<?php
namespace Slim\MVC\daos;

use PDO;
namespace Slim\MVC\entities\User;

class UserDAO
{
  private $db;

  public function __construct(PDO $db)
  {
    $this->db = $db;
  }

  public function findByPk(int $id): ?User
  {
    $sql = "select * from users where id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":id", $id, POD::PARAM_INT);
    $res = $stmt->execute();

    if ($res && $row = $stmt->fetch()) {
      $id = $row["id"];
      $username = $row["username"];
      $password = $row["password"];
      $role = $row["role"];
      $is_active = $row["is_active"];

      $user = new User();
      $user->setId($id);
      $user->setUsername($username);
      $user->setPassword($password);
      $user->setRole($role);
      $user->setIsActive($is_active);
    }

    return $user;
  }
}
