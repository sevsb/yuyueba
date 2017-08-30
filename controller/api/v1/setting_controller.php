<?php
include_once(dirname(__FILE__) . "/../../../config.php");
include_once(dirname(__FILE__) . "/../v1_base.php");

class setting_controller extends v1_base {
    public function preaction($action) {
    }


    public function listsettings_action() {
        $settings = setting::instance()->load_all();
        foreach ($settings as $k => $option) {
            if ($option["type"] == 1) {
                $settings[$k]["value"] = (int)$settings[$k]["value"];
            }
        }
        return $this->op("listsettings", $settings);
    }

    public function editsetting_action() {
        $id = get_request_assert("id");
        $val = get_request_assert("value");
        $ret = setting::instance()->update($id, $val);
        return $this->checkRet($ret);
    }

}













