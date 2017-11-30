<?php
include_once(dirname(__FILE__) . "/../config.php");

class Activity {
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
    public function title() {
        return $this->mSummary["title"];
    }
    public function info() {
        return $this->mSummary["info"];
    }
    public function images() {
        $images = $this->mSummary["images"];
        $images = $this->convert_json($images);
        return $images;
        logging::d("images", $images);
    }
    public function images_full_list(){
        $images = $this->images();

        //logging::d("images_full_list_1", $this->mSummary["images"]);
        logging::d("images_full_list_2", $images);
        if (empty($images)) {
            return $images;
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
            return $images;
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
            return $images;
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
    public function repeatend() {
        return $this->mSummary["repeatend"];
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
        $sheet = $this->mSummary["sheet"];
        $sheet = $this->convert_json($sheet);
        return $sheet;
    }
    public function clickcount() {
        return $this->mSummary["clickcount"];
    }
    public function status() {
        return $this->mSummary["status"];
    }
    public function calendar_id() {
        return $this->mSummary["calendar_id"];
    }
    public function detail_qcode() {
        $qcode = rtrim(UPLOAD_URL, "/") . "/qcode/" . $this->id() . ".jpg";
        logging::d('qcode_url,' , $qcode);
/*         
        logging::d('file_exists qcode', json_encode(file_exists($qcode)));
        if (!file_exists($qcode)) {
            logging::d('no qcode,' , $this->id() . ",now remake detail_qcode");
            $this->make_detail_qcode($this->id());
        } */
        return rtrim(UPLOAD_URL, "/") . "/qcode/" . $this->id() . ".jpg";
    }
    
    public function signed_user_list() {   
        $activity_id = $this->id();
        
        $all_sign = db_sign::inst()->all();
        $all_user = TempUser::all();
        
        $ret = [];
        if (empty($all_sign)) {
            return [];
        }
        foreach ($all_sign as $sign) {
            if ($sign["activity"] ==  $activity_id) {
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
        //$n = convert_to_string($n);
        $this->mSummary["images"] = $n;
    }
    public function setBegintime($n) {
        $this->mSummary["begintime"] = $n;
    }
    public function setEndtime($n) {
        $this->mSummary["endtime"] = $n;
    }
    public function setRepeatend($n) {
        $this->mSummary["repeatend"] = $n;
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
        $this->mSummary["sheet"] = $n;
    }
    public function setStatus($n) {
        $this->mSummary["status"] = $n;
    }
    public function setCalendarid($n) {
        $this->mSummary["calendar_id"] = $n;
    }

    public function make_detail_qcode($id){
        $page = "pages/activity/detail";
        $scene = "?id=$id";
        $imgsrc = Wxapi::get_wx_acode($page, $scene);
        $ret = Upload::save_qcode($imgsrc, $id);
        return $ret;
    }

    public function save() {
        $id = $this->id();
        if ($id == 0) {
            $id = db_activity::inst()->add($this->owner(), $this->title(), $this->info(), $this->convert_to_string($this->images()), $this->begintime(), $this->endtime(), $this->repeattype(), $this->repeatcount(), $this->repeatend(), $this->address(), $this->content(), $this->participants(), $this->convert_to_string($this->joinsheet()), $this->type(), $this->joinable(), $this->calendar_id());
            if ($id !== false) {
                $this->mSummary["id"] = $id;
                $ret = $this->make_detail_qcode($id);
            }
        } else {
            $id = db_activity::inst()->modify($this->id(), $this->title(), $this->info(), $this->convert_to_string($this->images()), $this->begintime(), $this->endtime(), $this->repeattype(), $this->repeatcount(), $this->repeatend(), $this->address(), $this->content(), $this->participants(), $this->convert_to_string($this->joinsheet()), $this->joinable(), $this->calendar_id(), $this->status());
        }
        return $id;
    }

    public function packInfo($detail = false) {
       return array(
            "id" => $this->id(),
            "type" => $this->type(),
            "owner" => $this->owner_detail(),
            "title" => $this->title(),
            "info" => $this->info(),
            "images" => $this->images(),
            "image_url_list" => $this->image_url_list(),
            "image_thumbnail_url_list" => $this->image_thumbnail_url_list(),
            "images_full_list" => $this->images_full_list(),
            "content" => $this->content(),
            "begintime" => $this->begintime(),
            "endtime" => $this->endtime(),
            "repeatend" => $this->repeatend(),
            "address" => $this->address(),
            "repeattype" => $this->repeattype(),
            "repeatcount" => $this->repeatcount(),
            "joinsheet" => $this->joinsheet(),
            "status" => $this->status(),
            "joinable" => $this->joinable(),
            "max_participants" => $this->max_participants(),
            "now_participants" => $this->now_participants(),
            "calendar_id" => $this->calendar_id(),
            "detail_qcode" => $this->detail_qcode(),
        );
    }

    public static function create($id) {
        $summary = db_activity::inst()->get($id);
        return new Activity($summary);
    }
    
    public static function build_one($type, $owner, $joinable, $participants, $title, $info, $content, $images, $begintime, $endtime, $repeatend, $address, $repeattype, $repeatcount, $joinsheet, $calendar_id) {
        $activity = new Activity();
            
        $activity->set_Type($type);
        $activity->setOwner($owner);
        
        $activity->setJoinable($joinable);
        $activity->setParticipants($participants);
        
        $activity->setTitle($title);
        $activity->setInfo($info);
        $activity->setContent($content);
        $activity->setImages($images);
        
        $activity->setBegintime($begintime);
        $activity->setEndtime($endtime);
        $activity->setRepeatend($repeatend);
        
        $activity->setAddress($address);
        
        $activity->setRepeattype($repeattype);
        $activity->setRepeatcount($repeatcount);
        $activity->setJoinsheet($joinsheet);
        $activity->setCalendarid($calendar_id);
        
        $activity->setStatus(0);

        $ret = $activity->save();
        
        return array("ret" => $ret, "activity" => $activity);
    }
    
    public static function edit_one($activity_id, $title,  $content,  $address,  $images) {
        
        $activity = Activity::oneById($activity_id);

        $activity->setTitle($title);
        $activity->setContent($content);
        $activity->setImages($images);
        $activity->setAddress($address);
        
        $ret = $activity->save();
        
        return array("ret" => $ret, "activity" => $activity);
    }
    
    public static function oneById($id) {
        $activities = self::cachedAll();
        foreach ($activities as $activity) {
            if ($activity->id() == $id) {
                return $activity;
            }
        }
        return null;
    }

    public static function all() {
        $items = db_activity::inst()->all();
        $arr = array();
        foreach ($items as $id => $summary) {
            $arr[$id] = new Activity($summary);
        }
        return $arr;
    }

    public static function &cachedAll() {
        $cache = cache::instance();
        $all = $cache->load("class.activity.all", null);
        if ($all === null) {
            $all = Activity::all();
            $cache->save("class.activity.all", $all);
        }
        return $all;
    }

    public static function remove($id) {
        return db_activity::inst()->remove($id);
    }
    
    public function cancel() {
        $this->setStatus(1);
        return $this->save();
    }
    
    public function start() {
        $this->setStatus(0);
        return $this->save();
    }
        
    function convert_json($string) {
        if (!is_string($string)) {
            logging::d('no string',$string);
            return $string;
        }else {
            logging::d('is string',$string);
            $string = json_decode($string);
            return $this->convert_json($string);
        }
    }

    function convert_to_string($json) {
        if (is_string($json)) {
            logging::d('is string',$json);
            return $json;
        }else {
            logging::d('no string',$json);
            $json = json_encode($json);
            return $this->convert_to_string($json);
        }
    }
};


