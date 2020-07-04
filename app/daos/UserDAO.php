<?php
namespace CamtemSlim\MVC\daos;

use PDO;
use CamtemSlim\MVC\entities\User;

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
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
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

  public function findByUser(string $username, string $password, int $is_active=1): array
  {
    $result =[
      "result" => true,
      "msg" => ""
    ];
    $sql="select * from users where username = :username and is_active = :is_active";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":username", $username, PDO::PARAM_STR);
    $stmt->bindValue(":is_active", $is_active, PDO::PARAM_INT);
    $res = $stmt->execute();

    if ($res && $row = $stmt->fetch()) {
      if (!password_verify($password, $row["password"])) {
            $result =[
              "result" => false,
              "msg" => "invalid username or password"
            ];
						return $result;
					}

					$_SESSION['user'] = [
						'id' => $row["id"],
						'username' => $row["username"],
						'role' => $row["role"]
					];
    }

    return $result;
  }
}
