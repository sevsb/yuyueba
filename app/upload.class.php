<?php
include_once(dirname(__FILE__) . "/../config.php");

class Upload {
    
    public static function upload_image() {
        $file = $_FILES['file'];
        logging::d('upload_image', 'file:' . json_encode($file));
        
        if(!is_uploaded_file($file['tmp_name'])){
          logging::d('upload_image', '�ϴ�CHECK:' . '����ͨ��HTTPPOST�ϴ���');
          return false;
        }
        
        if (!file_exists(UPLOAD_DIR)) {
            $ret = @mkdir(UPLOAD_DIR, 0777, true);
            if ($ret === false) {
                logging::d('upload_image', 'Ŀ¼����:' . '�ϴ�Ŀ¼����ʧ��');
                return false;
            }
        }
        
        $type = $file['type'];
        $extension = explode('/', $type);
        $extension = $extension[1];
        
        $new_name = md5(file_get_contents($file['tmp_name'])) . '.' . $extension;
        logging::d('upload_image', 'new_name:' . $new_name);
        
        if(file_exists(UPLOAD_DIR . "/" . $new_name)){
            return $new_name;
        }

        if(!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . "/" . $new_name)){
            logging::d('upload_image', 'move_uploaded:' . 'move_uploadedʧ��');
            return false;
        }
        return $new_name;
        
    }
    
    
    
    
    
    
}

?>