<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");

class activity_controller extends v1_base {
    private $mToken = null;
    private $mUser = null;

    public function preaction($action) {
        //$token = get_request_assert("token");
    }

    public function list_action() {
        $sort = get_request("sort", 0);
        $display = get_request("display", "0,20");

        $act1 = new Activity();
        $act2 = new Activity();
        $data = array(
            "activities" => array(
                $act1->packInfo(),
                $act2->packInfo(),
            ),
        );
        return $this->op("activities", $data);
    }

    public function search_action() {
        $s = get_request_assert("s");

        $act1 = new Activity();
        $act2 = new Activity();
        $data = array(
            "activities" => array(
                $act1->packInfo(),
                $act2->packInfo(),
            ),
        );
        return $this->op("activities", $data);
    }
    
    public function view_action() {
        $aid = get_request_assert("activity");

        $act1 = new Activity();
        $data = array(
            "info" => array(
                $act1->packInfo(true),
            ),
        );
        return $this->op("activity", $data);
    }

    public function join_action() {
    }

    public function mine_action() {
    }

    public function reply_action() {
    }

    public function organize_action() {
    }

    public function edit_action() {
    }

    public function viewmember_action() {
    }

    public function tipoff_action() {
    }
    
    public function upload_image_action() {
        $image = Upload::upload_image();   //先存图片
        if (!$image) {
            return array('op' => 'fail', "code" => 111, "reason" => '上传图片失败');
        }
        return array('op' => 'upload_image', "data" => $image);
    }
}













