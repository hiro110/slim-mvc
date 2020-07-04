<?php

$loggedinCheck = function ($request, $response, $next) {
    if (!array_key_exists('user', $_SESSION)) {
        // $response = $response->withRedirect('/admin/login');
        // return $response;
        return $response->withHeader('Location', '/admin/login')->withStatus(302);
    }

    $sql="select * from users where username = :username and is_active = :is_active";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":username", $username, PDO::PARAM_STR);
    $stmt->bindValue(":is_active", $is_active, PDO::PARAM_INT);
    $res = $stmt->execute();

    // if (array_key_exists('user', $_SESSION)) {
    //     if (array_key_exists('api_key', $_SESSION['user']) && array_key_exists('api_secret', $_SESSION['user'])) {
    //         $user = Model::factory('User')
    //             ->where('api_key', $_SESSION['user']['api_key'])
    //             ->where('api_secret', $_SESSION['user']['api_secret'])
    //             ->where('del', 0)
    //             ->select('id')
    //             ->find_one();

    //         if ($user) {
    //             // ログイン中かつ有効なユーザー
    //             return true;
    //         }
    //     }
    // }

    // return false;

    if($user){
        $response = $next($request, $response);
    } else {
        $response =  $response->withRedirect('/admin/login');
    }

    return $response;
};
