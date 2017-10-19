<?php
include_once(dirname(__FILE__) . "/../config.php");

class Upload {
    
    public static function upload_image() {
        $file = $_FILES['file'];
        logging::d('upload_image', 'file:' . json_encode($file));
        //$file_type = $file["type"];
        $file_type = explode('.', $file['name']);
        $file_type = $file_type[1];

        logging::d('upload_image', 'file_type:' . $file_type);

        
        if(!is_uploaded_file($file['tmp_name'])){
          //如果不是通过HTTP POST上传的
          logging::d('upload_image', '上传CHECK:' . '不是通过HTTPPOST上传的');
          return false;
        }
        
        if (!file_exists(UPLOAD_DIR)) {
            $ret = @mkdir(UPLOAD_DIR, 0777, true);
            if ($ret === false) {
                logging::d('upload_image', '目录创建:' . '上传目录创建失败');
                return false;
            }
        }
        
        $img_src = file_get_contents($file['tmp_name']);
        //logging::d("UPLOAD", "imgsrc:" . $img_src);
        $new_name = md5($img_src) . '.' . strtolower($file_type);
        if(file_exists(UPLOAD_DIR . "/" . $new_name)){
            return $new_name;
        }
        logging::d('upload_image', 'new_name:' . $new_name);
        if(!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . "/" . $new_name)){
            logging::d('upload_image', 'move_uploaded:' . 'move_uploaded失败');
            return false;
        }
        return $new_name;
        
    }
    
    
    
    
    
    
}

?>