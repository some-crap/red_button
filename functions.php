<?php
//
//
//
// Код доступен по лицензии
// Creative Commons «Attribution-NonCommercial» INTERNATIONAL
// https://creativecommons.org/licenses/by-nc/4.0/
//
//
//
// functions.php
function request_check(){
    if(DATA -> secret != VK_SECRET) { 
        exit();
    }
    
    if (DATA -> type == "confirmation" && DATA -> group_id == VK_GROUP_ID){
        echo VK_VERIFICATION;
        exit();
    }
    echo 'ok';
    fastcgi_finish_request();
    if (DATA -> type != "message_new"){
        exit();
    }
}

function check_language($str, $language){
    //global $peer_id;
    if($language == "en"){
        //message_send($peer_id, "en".$str.preg_match("/^[a-zA-Z]+$/u", $str));
        return preg_match("/^[a-zA-Z\-]+$/u", $str);
    } elseif($language == "ru"){
        //message_send($peer_id, "ru".$str.preg_match("/^[а-яА-ЯёЁ]+$/u",$str));
        return preg_match("/^[а-яА-ЯёЁ\-]+$/u",$str);
    } else {
        return false;
    }
}

function transliterate($textcyr = null, $textlat = null) {
    $cyr = array(
    'ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я',
    'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я');
    $lat = array(
    'zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', 'x', 'ya',
    'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', 'X', 'Ya');
    if($textcyr) return str_replace($cyr, $lat, $textcyr);
    else if($textlat) return str_replace($lat, $cyr, $textlat);
    else return null;
}

function message_send($peer_id, $text, $keyboard = null){
    if(is_null($keyboard)){
        $request_params = array('message' => $text, 'peer_id' => $peer_id, 'access_token' => VK_TOKEN, 'v' => VK_API_VERSION, 'random_id' => time().$peer_id.rand(0,5000));
    } else {
        $request_params = array('message' => $text, 'peer_id' => $peer_id, 'keyboard' => $keyboard, 'access_token' => VK_TOKEN, 'v' => VK_API_VERSION, 'random_id' => time().$peer_id.rand(0,5000));
    }
    $get_params = http_build_query($request_params);
    return file_get_contents('https://api.vk.com/method/messages.send?' . $get_params);
}

function get_user_condition(){
    global $link;
    global $from_id;
    $stmt = $link -> prepare("SELECT `user_condition` FROM `users` WHERE `vk_id` = ?");
    $stmt -> bind_param('i', $from_id);
    $stmt -> execute();
    $stmt -> store_result();
    $stmt->bind_result($user_condition);
    $stmt->fetch();
    return $user_condition;
}

function get_user_name($language){
    global $link;
    global $from_id;
    if($language == "ru"){
        $stmt = $link -> prepare("SELECT `name1`, `name2`, `name3` FROM `users` WHERE `vk_id` = ?");
        $stmt -> bind_param('i', $from_id);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($name1, $name2, $name3);
        $stmt -> fetch();
    }elseif($language == "en"){
        $stmt = $link -> prepare("SELECT `en_name1`, `en_name2`, `en_name3` FROM `users` WHERE `vk_id` = ?");
        $stmt -> bind_param('i', $from_id);
        $stmt -> execute();
        $stmt -> store_result();
        $stmt -> bind_result($name1, $name2, $name3);
        $stmt -> fetch();
    }
    if(strtolower($name3) == "отчества нет");
    return $name1." ".$name2." ".$name3;
}

function get_user_faculty(){
    global $link;
    global $from_id;
    $stmt = $link -> prepare("SELECT `faculty` FROM `users` WHERE `vk_id` = ?");
    $stmt -> bind_param('i', $from_id);
    $stmt -> execute();
    $stmt -> store_result();
    $stmt -> bind_result($faculty);
    $stmt -> fetch();
    return $faculty;
}

function get_user_course(){
    global $link;
    global $from_id;
    $stmt = $link -> prepare("SELECT `course` FROM `users` WHERE `vk_id` = ?");
    $stmt -> bind_param('i', $from_id);
    $stmt -> execute();
    $stmt -> store_result();
    $stmt -> bind_result($course);
    $stmt -> fetch();
    return $course;
}

function get_user_group(){
    global $link;
    global $from_id;
    $stmt = $link -> prepare("SELECT `group` FROM `users` WHERE `vk_id` = ?");
    $stmt -> bind_param('i', $from_id);
    $stmt -> execute();
    $stmt -> store_result();
    $stmt -> bind_result($group);
    $stmt -> fetch();
    return $group;
}

function get_user_id_photos(){
    global $link;
    global $from_id;
    $stmt = $link -> prepare("SELECT `id_photos` FROM `users` WHERE `vk_id` = ?");
    $stmt -> bind_param('i', $from_id);
    $stmt -> execute();
    $stmt -> store_result();
    $stmt -> bind_result($id_photos);
    $stmt -> fetch();
    return $id_photos;
}

function get_user_photos(){
    global $link;
    global $from_id;
    $stmt = $link -> prepare("SELECT `selfie_photos` FROM `users` WHERE `vk_id` = ?");
    $stmt -> bind_param('i', $from_id);
    $stmt -> execute();
    $stmt -> store_result();
    $stmt -> bind_result($selfie_photos);
    $stmt -> fetch();
    return $selfie_photos;
}

function is_user_in_db($vk_id){
    global $link;
    $stmt = $link -> prepare("SELECT 'id' FROM `users` WHERE `vk_id` = ?");
    $stmt -> bind_param('i', $vk_id);
    $stmt -> execute();
    $stmt -> store_result();
    if ($stmt -> num_rows() == 0){
        $stmt->close();
        return false;
    } else {
        $stmt->close();
        return true;
    }
}

function new_user($vk_id){
    global $link;
    $stmt = $link -> prepare("INSERT INTO `users` SET `vk_id` = ?, `is_reg` = ?");
    $time = time();
    $stmt -> bind_param('ii', $vk_id, $time);
    $stmt -> execute();
    $stmt->close();
    set_user_condition("wait_for_name_ru");
    return true;
}

function set_user_condition($condition){
    global $link;
    global $from_id;
    $stmt = $link -> prepare("UPDATE `users` SET `user_condition` = ? WHERE `vk_id` = ?");
    $stmt -> bind_param('si', $condition, $from_id);
    $stmt -> execute();
    $stmt->close();
    return true;
}

function set_user_course($course){
    if(!($course > 0 && $course < 7)){
        return false;
    }
    global $link;
    global $from_id;
    $stmt = $link -> prepare("UPDATE `users` SET `course` = ? WHERE `vk_id` = ?");
    $stmt -> bind_param('ii', $course, $from_id);
    $stmt -> execute();
    $stmt -> close();
    return true;
}

function set_user_faculty($faculty){
    if($faculty >=0 && $faculty <= 41){
        global $link;
        global $from_id;
        $stmt = $link -> prepare("UPDATE `users` SET `faculty` = ? WHERE `vk_id` = ?");
        $stmt -> bind_param('ii', $faculty, $from_id);
        $stmt -> execute();
        $stmt->close();
        return true;
    }
    return false;
}

function set_user_group($group){
    global $link;
    global $from_id;
    $stmt = $link -> prepare("UPDATE `users` SET `group` = ? WHERE `vk_id` = ?");
    $stmt -> bind_param('si', $group, $from_id);
    $stmt -> execute();
    $stmt -> close();
    return true;
}

function form_name($text, $language){
    $templates = array(
        "ru" => "Отправлено что-то не то.\nНапиши своё полное ФИО без дополнительных сиволов. (Если отчества нет, то просто не пишите его)\nНапример, Садовничий Виктор Антонович",
        "en" => "Отправлено что-то не то.\nНапиши своё полное ФИО без дополнительных сиволов. (Если отчества нет, то просто не пишите его)\nНапример, Sadovnichiy Viktor Anatolevich",
    );
    $names = explode(" ", $text);
    $amount = count($names);
    if($amount == 2){
        $name1 = trim(ucfirst(strtolower($names[0])));
        $name2 = trim(ucfirst(strtolower($names[1])));
        $name3 = "Отчества нет";
    }elseif($amount == 3){
        $name1 = trim(ucfirst(strtolower($names[0])));
        $name2 = trim(ucfirst(strtolower($names[1])));
        $name3 = trim(ucfirst(strtolower($names[2])));
    } else {
        global $from_id;
        message_send($from_id, $templates[$language]);
        exit();
    }
    return array($name1, $name2, $name3);
}

function insert_name($name, $langusage){
    global $from_id;
    global $link;
    //message_send($from_id, check_language($name[0], "en")." ".check_language($name[1], "en")." ".check_language($name[2], "en"));
    if($langusage == "ru" && check_language($name[0], "ru") == true && check_language($name[1], "ru") == true && check_language($name[2], "ru") == true){
        $stmt = $link -> prepare("UPDATE `users` SET `name1` = ?, `name2` = ?, `name3` = ? WHERE `vk_id` = ?");
        $stmt -> bind_param('sssi', $name[0], $name[1], $name[2], $from_id);
        $stmt -> execute();
        $stmt -> close();
        if($name[2] == "Отчества нет"){
            $stmt = $link -> prepare("UPDATE `users` SET `en_name1` = ?, `en_name2` = ?, `en_name3` = ? WHERE `vk_id` = ?");
            $stmt -> bind_param('sssi', transliterate($name[0]), transliterate($name[1]), $name[2], $from_id);
            $stmt -> execute();
            return 1;
        } else {
            $stmt = $link -> prepare("UPDATE `users` SET `en_name1` = ?, `en_name2` = ?, `en_name3` = ? WHERE `vk_id` = ?");
            $stmt -> bind_param('sssi', transliterate($name[0]), transliterate($name[1]), transliterate($name[2]), $from_id);
            $stmt -> execute();
            return 1;
        }
    }elseif ($langusage == "en"  && check_language($name[0], "en") && check_language($name[1], "en") && check_language($name[2], "en")){
        $stmt = $link -> prepare("UPDATE `users` SET `en_name1` = ?, `en_name2` = ?, `en_name3` = ? WHERE `vk_id` = ?");
        $stmt -> bind_param('sssi', $name[0], $name[1], $name[2], $from_id);
        $stmt -> execute();
        return 1;
    }else{
        return 0;
    }
}

function keyboard_gen($page = 1){
    global $peer_id;
    $facs = json_decode('{"facs": ["Мехмат", "ВМК", "ФизФак", "ХимФак", "ФНМ", "Биофак", "ФББ", "Факультет почвоведения", "ГеолФак", "Геогрфак", "ФФМ", "ФФФХИ", "Биотех", "ФКИ", "ИстФак", "ФилФак", "ФилосФак", "ЭкономФак", "ЮрФак", "ЖурФак", "ПсихФак", "ИСАА", "СоцФак", "ФИЯР", "ФГУ", "ФМП", "Факультет искусств", "ФГП", "ФПО", "Факультет политологии", "ВШБ", "МШЭ", "ВШП", "ВШГА (админ)", "ВШГА (аудит)", "ВШУИ", "ВШИБ", "ВШССН", "ВШТ", "ВШКПУГС", "СУНЦ", "Гимназия-интернат"]}');//json_decode(file_get_contents("faculties.json"));
    $facs = $facs -> facs;
    $amount = count($facs);
    $to = $page * 3 + $page - 1;
    $c = 3;
    $keyboard = '{
        "one_time": false,
        "buttons": [';
    while($c >= 0){
        if($to - $c < $amount){
            $keyboard .= '[
                {
                "action": {
                "type": "text",
                "label": "'.$facs[$to-$c].'",
                "payload": "{\"button\": \"fac_'.$to-$c.'\"}"
                },
                "color": "primary"
                }
            ],';
        }
        $c--;
    }
    if ($page == 1){
        $keyboard .= '[
            {
                "action": {
                "type": "text",
                "label": ">>",
                "payload": "{\"button\": \"page_'.($page+1).'\"}"
                },
                "color": "primary"
                }]';
        $keyboard .= ']
    }';
    return $keyboard;
    }
    if ($page * 4 >= $amount){
        $keyboard .= '[{
            "action": {
            "type": "text",
            "label": "<<",
            "payload": "{\"button\": \"page_'.($page-1).'\"}"
            },
            "color": "primary"
            }]';
        $keyboard .= ']
    }';
        return $keyboard;
    }
    $keyboard .= '[{
        "action": {
        "type": "text",
        "label": "<<",
        "payload": "{\"button\": \"page_'.($page-1).'\"}"
        },
        "color": "primary"
        },
        {
            "action": {
            "type": "text",
            "label": ">>",
            "payload": "{\"button\": \"page_'.($page+1).'\"}"
            },
            "color": "primary"
            }]';
    $keyboard .= ']
}';
    return $keyboard;
    //message_send($peer_id, $amount);
}
