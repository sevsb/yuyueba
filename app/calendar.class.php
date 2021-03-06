<?php
include_once(dirname(__FILE__) . "/../config.php");

class Calendar {
    private $mSummary = null;

    public function __construct($summary = array()) {
        if (empty($summary)) {
            $summary = array(
                "id" => 0,
            );
        }
        $this->mSummary = $summary;
    }

    public function id() {
        return $this->mSummary["id"];
    }

    public function owner() {
        return $this->mSummary["owner"];
    }
    public function owner_detail() {
        $type = $this->type();
        $owner_id = $this->owner();
        if ($type == 1) {
            $user = TempUser::oneById($owner_id);
            return $user ? $user->packInfo() : null;
        }else if ($type == 2) {
            $organization = Organization::oneById($owner_id);
            return $organization ? $organization->packInfo() : null;
        }else {
            return false;
        }
    }
    
    public function activity_list() {
        return json_decode($this->mSummary["activity_list"]);
    }
    
    public function activity_detail_list_by_date() {
        $activity_list = $this->activity_list();
        $all_activity = Activity::all();
        
        $array = [];
        if (empty($activity_list)) {
            return null;
        }
        foreach ($activity_list as $aid ) {
            foreach ($all_activity as $act) {
                if ($aid == $act->id()) {
                    $act_start_time = $act->begintime();
                    $act_start_date = $act_start_time - (($act_start_time + 60 * 60 * 8) % (60 * 60 * 24));
                    $array[$act_start_date][$aid] = $act->packInfo();
                }
            }
        }
        return $array;
    }
    
    public function title() {
        return $this->mSummary["title"];
    }
    public function info() {
        return $this->mSummary["info"];
    }
    public function images() {
        return json_decode($this->mSummary["images"]);
    }
    public function images_full_list(){
        $images = json_decode($this->mSummary["images"]);
        if (empty($images)) {
            return images;
        }
        $arr = [];
        foreach ($images as $image) {
            $a = [];
            $a["name"] = $image;
            $a["image_url"] = rtrim(UPLOAD_URL, "/") . "/" . $image;
            $a["thumbnail_url"] = rtrim(THUMBNAIL_URL, "/") . "/thumbnail-$image";
            array_push($arr, $a);
        }
        return $arr;
    }
    public function image_url_list() {
        $images = json_decode($this->mSummary["images"]);
        if (empty($images)) {
            return images;
        }
        $arr = [];
        foreach ($images as $image) {
            array_push($arr, rtrim(UPLOAD_URL, "/") . "/" . $image);
        }
        return $arr;
    }
    public function image_thumbnail_url_list() {
        $images = json_decode($this->mSummary["images"]);
        if (empty($images)) {
            return images;
        }
        $arr = [];
        foreach ($images as $image) {
            array_push($arr, rtrim(THUMBNAIL_URL, "/") . "/thumbnail-$image");
        }
        return $arr;
    }
    public function createtime() {
        return $this->mSummary["createtime"];
    }
    public function modifytime() {
        return $this->mSummary["modifytime"];
    }
    public function begintime() {
        return $this->mSummary["begintime"];
    }
    public function endtime() {
        return $this->mSummary["endtime"];
    }
    public function repeattype() {
        return $this->mSummary["repeattype"];
    }
    public function repeatcount() {
        return $this->mSummary["repeatcount"];
    }
    public function deadline() {
        return $this->mSummary["deadline"];
    }
    public function address() {
        return $this->mSummary["address"];
    }
    public function content() {
        return $this->mSummary["content"];
    }
    public function max_participants() {
        return $this->mSummary["participants"];
    }
    public function participants() {
        return $this->mSummary["participants"];
    }
    
    public function now_participants() {
        return count($this->signed_user_list());
    }
    public function type() {
        return $this->mSummary["type"];
    }
    public function joinable() {
        return $this->mSummary["joinable"];
    }
    public function joinsheet() {
        return json_decode($this->mSummary["sheet"]);
    }
    public function clickcount() {
        return $this->mSummary["clickcount"];
    }
    public function status() {
        return $this->mSummary["status"];
    }
       
    public function subscribe($userid) {
        logging::d("userid", $userid);
        if (Subscribe::load(0, $this->id(), $userid)) {
            return true;
        }
        $subscribe = Subscribe::subscribe_it(0, $this->id(), $userid);
        return $subscribe;
    }
    
    public function unsubscribe($userid) {
        $subscribe = Subscribe::unsubscribe_it(0, $this->id(), $userid);
        return $subscribe;
    }
    
    public function signed_user_list() {   
        $Calendar_id = $this->id();
        
        $all_sign = db_sign::inst()->all();
        $all_user = TempUser::all();
        
        $ret = [];
        if (empty($all_sign)) {
            return [];
        }
        foreach ($all_sign as $sign) {
            if ($sign["Calendar"] ==  $Calendar_id) {
                $s["id"] = $sign['id'];
                $s["sheet"] = json_decode($sign['sheet']);
                $s["user"] = $all_user[$sign['user']]->packInfo();
                $ret[$sign["id"]] = $s;
            }
        }
        return $ret;
    }

    public function setTitle($n) {
        $this->mSummary["title"] = $n;
    }
    public function set_Type($n) {
        $this->mSummary["type"] = $n;
    }
    public function setOwner($n) {
        $this->mSummary["owner"] = $n;
    }
    public function setJoinable($n) {
        $this->mSummary["joinable"] = $n;
    }
    public function setParticipants($n) {
        $this->mSummary["participants"] = $n;
    }
    public function setInfo($n) {
        $this->mSummary["info"] = $n;
    }
    public function setContent($n) {
        $this->mSummary["content"] = $n;
    }
    public function setImages($n) {
        $this->mSummary["images"] = json_encode($n);
    }
    public function setBegintime($n) {
        $this->mSummary["begintime"] = $n;
    }
    public function setEndtime($n) {
        $this->mSummary["endtime"] = $n;
    }
    public function setDeadline($n) {
        $this->mSummary["deadline"] = $n;
    }
    public function setAddress($n) {
        $this->mSummary["address"] = $n;
    }
    public function setRepeattype($n) {
        $this->mSummary["repeattype"] = $n;
    }
    public function setRepeatcount($n) {
        $this->mSummary["repeatcount"] = $n;
    }
    public function setJoinsheet($n) {
        $this->mSummary["sheet"] = json_encode($n);
    }
    public function setStatus($n) {
        $this->mSummary["status"] = $n;
    }
    public function set_activity_list($n) {
        $this->mSummary["activity_list"] = json_encode($n);
    }


    public function save() {
        $id = $this->id();
        if ($id == 0) {
            $id = db_calendar::inst()->add($this->title(), $this->content(), $this->type(), $this->owner());
            if ($id !== false) {
                $this->mSummary["id"] = $id;
            }
        } else {
            $id = db_calendar::inst()->modify($this->id(), $this->title(), $this->content(), $this->type(), $this->owner(), json_encode($this->activity_list()));
        }
        return $id;
    }

    public function packInfo($detail = false) {
       return array(
            "id" => $this->id(),
            "type" => $this->type(),
            "owner" => $this->owner_detail(),
            "title" => $this->title(),
            "content" => $this->content(),
            "status" => $this->status(),
            "activity_list" => $this->activity_list(),
            "activity_detail_list_by_date" => $this->activity_detail_list_by_date()
        );
    }

    public static function create($id) {
        $summary = db_calendar::inst()->get($id);
        return new Calendar($summary);
    }
    
    public static function oneById($id) {
        $activities = self::cachedAll();
        foreach ($activities as $Calendar) {
            if ($Calendar->id() == $id) {
                return $Calendar;
            }
        }
        return null;
    }

    public static function all() {
        $items = db_calendar::inst()->all();
        $arr = array();
        foreach ($items as $id => $summary) {
            $arr[$id] = new Calendar($summary);
        }
        return $arr;
    }

    public static function &cachedAll() {
        $cache = cache::instance();
        $all = $cache->load("class.Calendar.all", null);
        if ($all === null) {
            $all = Calendar::all();
            $cache->save("class.Calendar.all", $all);
        }
        return $all;
    }

    public static function remove($id) {
        return db_calendar::inst()->remove($id);
    }
    
    public static function cancel($id) {
        return db_calendar::inst()->cancel($id);
    }
    
    public static function get_all_my_list($userid) {
               
        $my_created_list = self::get_my_created_list($userid);
        $my_org_created_list = self::get_my_org_created_list($userid);
        $my_subscribe_list = self::my_subscribe_list($userid);
        
        
        foreach ($my_org_created_list as $id => $activity) {
            $my_org_created_list[$id]['owner_avatar'] = rtrim(UPLOAD_URL, "/") . "/" . $activity['owner_avatar'];
        }
        
        foreach ($my_subscribe_list as $id => $activity) {
            if ($activity['type'] == 2) {
                $my_subscribe_list[$id]['owner_avatar'] = rtrim(UPLOAD_URL, "/") . "/" . $activity['owner_avatar'];
            }
        }
        
        $data = array(
            "my_created_list" => $my_created_list,
            "my_org_created_list" => $my_org_created_list,
            "my_subscribe_list" => $my_subscribe_list,
        );
        return $data;
    }
    
    public static function get_my_created_list($userid) {
        $sql = "
        select a.*, b.avatar owner_avatar
        from yyba_calendar a 
        join yyba_tempuser b 
        on b.id = a.owner 
        where a.owner = $userid and a.type = 1";
        return db_base::inst()->do_query($sql);
    }
    public static function get_my_org_created_list($userid) {
        $sql = "
        select a.*,c.avatar owner_avatar
        from yyba_calendar a 
        join yyba_organization_member b 
        on a.owner = b.organization
        join yyba_organization c 
        on c.id = a.owner 
        where a.type = 2 and b.user = $userid";
        return db_base::inst()->do_query($sql);
    }

    public static function my_subscribe_list($userid) {
        $sql = "
        select a.*,c.avatar owner_avatar
        from yyba_calendar a 
        join yyba_subscribe b 
        on a.id = b.calendar 
        join yyba_tempuser c 
        on c.id = a.owner 
        where b.user = $userid and a.type = 1 
        union 
        select x.*,z.avatar owner_avatar
        from yyba_calendar x 
        join yyba_subscribe y 
        on x.id = y.calendar 
        join yyba_organization z 
        on z.id = x.owner 
        where y.user = $userid and x.type = 2";
        return db_base::inst()->do_query($sql);
    }
    
    
    
};

