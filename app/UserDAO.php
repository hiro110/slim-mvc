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

  public function findByUser(string $username, string $password, bool $is_active=true): array
  {
    $result =[];
    $sql = "select * from users where username = :username and is_active = :is_active";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":username", $username, POD::PARAM_STR);
    $stmt->bindValue(":is_active", $is_active, POD::PARAM_BOOL);
    $res = $stmt->execute();
    $result["result"] = $res;

    if ($res && $row = $stmt->fetch()) {
      if (password_verify($password, $row["password"])) {
        // sessionに格納。。
        $_SESSION['username'] = $user->username;
        $_SESSION['role'] = $user->role;
        return $result;
      }
    }
    $result["msg"] = "invalid username or password";
    return $result;
  }
}
