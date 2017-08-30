<?php
include_once(dirname(__FILE__) . "/../config.php");

class User {
    private $mSummary = null;
    private $mGroups = null;

    public function User($summary = array()) {
        if (empty($summary)) {
            $summary = array(
                "id" => 0,
                "username" => "",
                "password" => "",
                "telephone" => "",
                "email" => "",
                "groups" => "",
                "comments" => "",
                "create_time" => "",
                "active_time" => "",
                "last_login" => "",
                "token" => "",
                "status" => 0,
            );
        }
        $this->mSummary = $summary;
    }

    public function id() {
        return $this->mSummary["id"];
    }

    public function username() {
        return $this->mSummary["username"];
    }

    public function password() {
        return $this->mSummary["password"];
    }

    public function nickname() {
        return $this->mSummary["nickname"];
    }

    public function telephone() {
        return $this->mSummary["telephone"];
    }

    public function email() {
        return $this->mSummary["email"];
    }

    public function comments() {
        return $this->mSummary["comments"];
    }

    public function setUsername($n) {
        $this->mSummary["username"] = $n;
    }

    public function setPassword($p) {
        $this->mSummary["password"] = $p;
    }

    public function setNickname($n) {
        $this->mSummary["nickname"] = $n;
    }

    public function setTelephone($t) {
        $this->mSummary["telephone"] = $t;
    }

    public function setEmail($mail) {
        $this->mSummary["email"] = $mail;
    }

    public function setComments($c) {
        $this->mSummary["comments"] = $c;
    }

    public function gids() {
        $gids = $this->mSummary["groups"];
        if (empty($gids)) {
            $gids = array();
        } else {
            $gids = explode(",", $gids);
        }
        return $gids;
    }

    public function groups() {
        if ($this->mGroups === null) {
            $this->mGroups = array();
            $groups = self::cachedAllGroups();
            $gids = $this->gids();
            foreach ($gids as $gid) {
                if (isset($groups[$gid])) {
                    $this->mGroups []= $groups[$gid];
                }
            }
        }
        return $this->mGroups;
    }

    public function hasPerm($pkey) {
        $groups = $this->groups();
        foreach ($groups as $group) {
            if ($group->hasPerm($pkey)) {
                return true;
            }
        }
        return false;
    }

    public function joinGroup($gid) {
        $gids = $this->gids();
        if (!in_array($gid, $gids)) {
            $gids []= $gid;
        }
        $this->mSummary["groups"] = implode(",", $gids);
    }

    public function leaveGroup($gid) {
        $gids = $this->gids();
        foreach ($gids as $k =>  $g) {
            if ($g == $gid) {
                unset($gids[$k]);
            }
        }
        $this->mSummary["groups"] = implode(",", $gids);
    }

    public function save() {
        $id = $this->id();
        if ($id == 0) {
            $id = db_user::inst()->add($this->username(), $this->password(), $this->nickname(), $this->telephone(), $this->email(), $this->mSummary["groups"], $this->comments());
            if ($id !== false) {
                $this->mSummary["id"] = $id;
            }
        } else {
            $id = db_user::inst()->modify($id, $this->username(), $this->password(), $this->nickname(), $this->telephone(), $this->email(), $this->mSummary["groups"], $this->comments());
        }
        return $id;
    }

    private static function cachedAllGroups() {
        $cache = cache::instance();
        $groups = $cache->load("class.user.allgroups", null);
        if ($groups === null) {
            $groups = Group::all();
            $cache->save("class.user.allgroups", $groups);
        }
        return $groups;
    }

    public function packInfo($pack_all_groups = true) {
        $groupInfo = array();
        if ($pack_all_groups) {
            $groups = self::cachedAllGroups();
            $gids = $this->gids();
            foreach ($groups as $gid => $group) {
                $groupInfo[$gid] = $group->packInfo(false);
                $groupInfo[$gid]["join"] = 0;
            }
            foreach ($gids as $gid) {
                if (isset($groups[$gid])) {
                    $groupInfo[$gid]["join"] = 1;
                }
            }
        } else {
            $groups = $this->groups();
            $groupInfo = array();
            foreach ($groups as $group) {
                $groupInfo []= $group->packInfo(false);
            }
        }

        return array(
            "id" => $this->id(),
            "username" => $this->username(), 
            "password" => $this->password(), 
            "nickname" => $this->nickname(), 
            "telephone" => $this->telephone(), 
            "email" => $this->email(), 
            "comments" => $this->comments(), 
            "groups" => $groupInfo
        );
    }

    public static function create($uid) {
        $user = db_user::inst()->get($uid);
        return new User($user);
    }

    public static function all($include_deleted = false) {
        $users = db_user::inst()->all();
        $arr = array();
        foreach ($users as $uid => $user) {
            if (!$include_deleted) {
                if ($user["status"] == db_user::STATUS_DELETED) {
                    continue;
                }
            }
            $arr[$uid] = new User($user);
        }
        return $arr;
    }

    public static function &cachedAll() {
        $cache = cache::instance();
        $all = $cache->load("class.user.all", null);
        if ($all === null) {
            $all = User::all();
            $cache->save("class.user.all", $all);
        }
        return $all;
    }

    public static function oneByName($username) {
        $users = self::cachedAll();
        foreach ($users as $user) {
            if ($user->username() == $username) {
                return $user;
            }
        }
        return null;
    }

    public static function remove($uid) {
        return db_user::inst()->remove($uid);
    }
};

