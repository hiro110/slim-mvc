<?php
namespace App\daos;

use PDO;
use App\Entities\User;

class UserDAO
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByPk(int $id, int $is_active = 1): ?User
    {
        $sql = "select * from users where id = :id and is_active = :is_active";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":is_active", $is_active, PDO::PARAM_INT);
        $res = $stmt->execute();

        $user = new User();
        if ($res && $row = $stmt->fetch()) {
            $id = intVal($row["id"]);
            $username = $row["username"];
            $password = $row["password"];
            $role = intVal($row["role"]);
            $is_active = ($row["is_active"] == "1");

            // $user = new User();
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

    public function findAllUsers(): array
    {
        $sql = "select * from users where is_active = :is_active";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":is_active", 1, PDO::PARAM_INT);
        $res = $stmt->execute();

        $users = [];
        if ($res && $row = $stmt->fetch()) {
            $id = intVal($row["id"]);
            $username = $row["username"];
            $role = intVal($row["role"]);

            $user = new User();
            $user->setId($id);
            $user->setUsername($username);
            $user->setRole($role);

            $users[$id] = $user;
        }

        return $users;
    }
}
