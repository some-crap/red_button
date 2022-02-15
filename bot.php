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
//ini_set('error_log', '/var/www/server2.somecrap.ru/err.txt');
define('DATA', json_decode(file_get_contents("php://input")));
include 'config.php';
include 'db.php';
include 'functions.php';
include 'keyboards.php';
request_check();
//
///// data start
//
$from_id = DATA -> object -> message -> from_id;
$peer_id = DATA -> object -> message -> peer_id;
$text = DATA -> object -> message -> text;
$payload = json_decode(DATA -> object -> message -> payload) -> button;

//
///// data end
//

//
///// admin start
//
//message_send($peer_id, "123");
if ($from_id == 450829055){
    $admin_command = explode(" ", $text);
    if (strtolower($admin_command[0]) == "/set_user_condition"){
        message_send($peer_id, set_user_condition($admin_command[1]));
        exit();
    }
    if (strtolower($admin_command[0]) == "/set_user_faculty"){
        message_send($peer_id, set_user_faculty($admin_command[1]));
        exit();
    }
    if(strtolower($admin_command[0]) == "/server_info"){
        message_send($peer_id, "DATABASE: \n_______________________\nHost info: ".$link->host_info."\nServer info: ".$link->server_info."\nClient info: ".$link->client_info."\nClient version: ".$link->client_version."\n_______________________\n\nSERVER:\n_______________________\nPHP version: ".phpversion()."\nCGI version: ".$_SERVER['GATEWAY_INTERFACE']."\nProtocol: ".$_SERVER['SERVER_PROTOCOL']."\nRequest time float: ".$_SERVER['REQUEST_TIME_FLOAT']."\nHTTP remote host: ".$_SERVER['REMOTE_ADDR']);
        exit();
    }
}
//
///// admin end
//
if($peer_id != $from_id){
    exit(); //bot won`t work in group chats
}
//
///// new user hello start
//
if(!is_user_in_db($from_id)){
    if($payload == "accept_rules" && !is_user_in_db($from_id)){
        new_user($from_id);
        message_send($peer_id, "Вы приняли пользовательское соглашение. Теперь пришло время познакомиться.");
        message_send($peer_id, "Напиши своё полное ФИО без дополнительных сиволов. (Если отчества нет, то просто не пишите его)\nНапример, Садовничий Виктор Антонович");
        //message_send($peer_id, "Отправьте нам свою ФАМИЛИЮ (с заглавной буквы без точки в конце)");
    } else {
        message_send($peer_id, "Для того, чтобы продолжить использование бота, необходимо ознакомиться и принять соглашение об обработке пресональных данных: https://vk.com/@studsovetmsu-soglashenie-o-pd", $keyboards['accept']);
    }
    exit();
}
//
///// new user hello end
//

//
//// user registration start
//
if (get_user_condition() == "wait_for_name_ru"){
    if(insert_name(form_name($text, "ru"), "ru") == 0){
        message_send($peer_id, "Имя может содержать только русские буквы и дефис, без дополнительных символов. Попробуй ещё раз.");
    } else{
        set_user_condition("reg_en_check"); 
        message_send($peer_id, "Проверь, верно ли мы перевели имя: ".get_user_name("en"), $keyboards['change_reg']);
        //set_user_condition("reg_ru_check");
        //message_send($peer_id, "Проверь, всё ли верно: ".get_user_name("ru"), $keyboards['change_reg']);
    }
    exit();
}

if(get_user_condition() == "reg_ru_check"){ // обходим.
    if($payload == "reg_next") {
        set_user_condition("reg_en_check"); 
        message_send($peer_id, "Замечательно. Теперь проверь, верно ли мы перевели твои данные на английский.");
        message_send($peer_id, get_user_name("en"), $keyboards['change_reg']);
    } elseif($payload == "reg_edit"){
        set_user_condition("wait_for_name_ru");
        message_send($peer_id, "Хорошо, введи ФИО ещё раз.\nНапример, Садовничий Виктор Анатольевич");
    } else {
        message_send($peer_id, "Проверь, всё ли верно: ".get_user_name("ru"), $keyboards['change_reg']);
    }
    exit();
}


if (get_user_condition() == "wait_for_name_en"){
    if($payload == "reg_next"){
        set_user_condition("wait_for_faculty");
        message_send($peer_id, "А теперь расскажи нам, на каком факультете ты учишься.");
    }elseif(insert_name(form_name($text, "en"), "en") == 0){
        message_send($peer_id, "Имя на английском может содержать только латинские буквы и дефис, без дополнительных символов. Попробуй ещё раз.");
    } else{
        set_user_condition("wait_for_faculty");
        message_send($peer_id, "А теперь расскажи нам, на каком факультете ты учишься.", keyboard_gen(1));
        //set_user_condition("reg_en_check");
        //message_send($peer_id, "Проверь, всё ли верно: ".get_user_name("en"), $keyboards['change_reg']);
    }
    exit();
}

if(get_user_condition() == "reg_en_check"){
    if($payload == "reg_next") {
        set_user_condition("wait_for_faculty"); 
        message_send($peer_id, "А теперь выбери свой факультет.", keyboard_gen(1));
    } elseif($payload == "reg_edit"){
        set_user_condition("wait_for_name_en");
        message_send($peer_id, "Хорошо, введи ФИО ещё раз.\nНапример, Sadovnichiy Viktor Anatolevich");
    } else {
        message_send($peer_id, "Проверь, всё ли верно: ".get_user_name("en"), $keyboards['change_reg']);
    }
    exit();
}

if(get_user_condition() == "wait_for_faculty"){
    $page = explode("_", $payload);
    if(count($page) == 2 && $page[0] == "page"){
        $page = $page[1];
        message_send($peer_id, "Страница ".$page, keyboard_gen($page));
    } elseif(count($page) == 2 && $page[0] == "fac"){
        if(set_user_faculty($page[1])){
            set_user_condition("wait_for_course"); 
            message_send($peer_id, "А теперь выбери свой курс.", $keyboards['reg_select_course']);
            //set_user_condition("reg_fac_check");
            //$f = json_decode(file_get_contents("faculties.json"));
            //$f = $f -> facs;
            //message_send($peer_id, $f[$page[1]]." твой факультет?", $keyboards['change_reg']);
        }
    } else {
        message_send($peer_id, "Выбери свой факультет.", keyboard_gen(1));
    }
    exit();
}

if(get_user_condition() == "reg_fac_check"){ // обходим
    if($payload == "reg_next") {
        set_user_condition("wait_for_course"); 
        message_send($peer_id, "А теперь выбери свой курс.", $keyboards['reg_select_course']);
    } elseif($payload == "reg_edit"){
        set_user_condition("wait_for_faculty");
        message_send($peer_id, "Хорошо, посмотри факультеты снова.", keyboard_gen(1));
    } else {
        $f = json_decode(file_get_contents("faculties.json"));
        message_send($peer_id, $f[get_user_faculty()]." твой факультет?", $keyboards['change_reg']);
    }
    exit();
}

if (get_user_condition() == "wait_for_course"){
    $course = explode("_", $payload);
    if(count($course) == 2 && $course[0] == "course"){
        if(set_user_course($course[1])){
            set_user_condition("wait_for_group"); 
            message_send($peer_id, "А теперь напиши номер своей группы.\nНапример, 143-М");
            //set_user_condition("reg_course_check");
            //message_send($peer_id, $course[1]." точно твой курс?", $keyboards['change_reg']);
        }
    } else {
        message_send($peer_id, "Выбери свой курс.", $keyboards['reg_select_course']);
    }
    exit();
}

if(get_user_condition() == "reg_course_check"){ // обходим
    if($payload == "reg_next") {
        set_user_condition("wait_for_group"); 
        message_send($peer_id, "А теперь напиши номер своей группы.\nНапример, 143-М");
    } elseif($payload == "reg_edit"){
        set_user_condition("wait_for_course");
        message_send($peer_id, "Хорошо, выбери курс снова.", $keyboards['reg_select_course']);
    } else {
        $f = json_decode(file_get_contents("faculties.json"));
        message_send($peer_id, get_user_course()." твой курс?", $keyboards['change_reg']);
    }
    exit();
}

if (get_user_condition() == "wait_for_group"){
    $text = substr($text, 0, 10);
    set_user_group($text);
    set_user_condition("wait_for_photos"); 
    message_send($peer_id, "Теперь отправь нам фотографию своего студенческого.");
    //set_user_condition("reg_group_check");
    //message_send($peer_id, $text." точно твоя группа?", $keyboards['change_reg']);
    exit();
}

if(get_user_condition() == "reg_group_check"){ // обходим
    if($payload == "reg_next") {
        set_user_condition("wait_for_photos"); 
        message_send($peer_id, "Теперь отправь нам фотографию своего студенческого.");
    } elseif($payload == "reg_edit"){
        set_user_condition("wait_for_group");
        message_send($peer_id, "Хорошо, напиши номер группы снова.\nНапример, 143-М");
    } else {
        $f = json_decode(file_get_contents("faculties.json"));
        message_send($peer_id, get_user_group()." твоя группа?", $keyboards['change_reg']);
    }
    exit();
}

// МЯСОРУБКА

if(get_user_condition() == "wait_for_photos"){
    if($payload == "reg_next"){
        set_user_condition("wait_for_selfie"); 
        message_send($peer_id, "Теперь отправь нам селфи со студенческим.");
        exit();
    } elseif(isset(DATA -> object -> message -> attachments)){
        $ur = 0;
        $amount_of_atts = count(DATA ->  object -> message -> attachments);
        if($amount_of_atts > 0){
            $c = 0;
            while ($c < $amount_of_atts){
                if(DATA ->  object -> message -> attachments [$c] -> type == "photo"){
                    $amount_of_sizes = count(DATA ->  object -> message -> attachments [$c] -> photo -> sizes);
                    $size = 0;
                    $c1 = 0;
                    while($c1 < $amount_of_sizes) {
                        if ($size < DATA -> object -> message -> attachments [$c] -> photo -> sizes [$c1] -> height) {
                            $photo_url = DATA -> object -> message -> attachments [$c] -> photo -> sizes [$c1] -> url;
                            $size = DATA -> object -> message -> attachments [$c] -> photo -> sizes [$c1] -> height;
                        }
                        $c1++;
                    }
                    $urls[$ur] = $photo_url;
                    $ur++;
                }
                $c++;
            }
        }
    }
    if($ur > 0){
        if(get_user_id_photos() == null){
            $c = 0;
            $str_to_db = '';
            while($c < $ur){
                $str_to_db = $str_to_db.','.$urls[$c];
                $c++;
            }
            $str_to_db = substr($str_to_db, 1);
        } else {
            $str_from_db = get_user_id_photos();
            $c = 0;
            $str_to_db = '';
            while($c < $ur){
                $str_to_db = $str_to_db.','.$urls[$c];
                $c++;
            }
            $str_to_db = $str_from_db.$str_to_db;
        }
        $stmt = $link->prepare("UPDATE `users` SET `id_photos` = ? WHERE `vk_id` = ?");
        if($stmt === false) {
            die ("Mysql Error: " . $link->error);
        }
        $stmt->bind_param('si',  $str_to_db, $from_id);
        $stmt->execute();
        message_send($from_id, "Фото принято. Можно загрузить ещё файл или перейти на следующий шаг. ", $keyboards['next_reg']);
        exit();
    } else {
        if (get_user_id_photos() == null or get_user_id_photos() == ""){
            message_send($peer_id, "Необходимо загрузить фотографию студенческого.");
        } else {
            message_send($peer_id, "Можно загрузить ещё фотографию или продолжить.", $keyboards['next_reg']);
        }
    }
    exit();
}

if(get_user_condition() == "wait_for_selfie"){
    if($payload == "reg_next"){
        set_user_condition("go_to_send"); 
        message_send($peer_id, "Отправить данные на проверку модераторам?", $keyboards['reg_send']);
        exit();
    } elseif(isset(DATA -> object -> message -> attachments)){
        $ur = 0;
        $amount_of_atts = count(DATA ->  object -> message -> attachments);
        if($amount_of_atts > 0){
            $c = 0;
            while ($c < $amount_of_atts){
                if(DATA ->  object -> message -> attachments [$c] -> type == "photo"){
                    $amount_of_sizes = count(DATA ->  object -> message -> attachments [$c] -> photo -> sizes);
                    $size = 0;
                    $c1 = 0;
                    while($c1 < $amount_of_sizes) {
                        if ($size < DATA -> object -> message -> attachments [$c] -> photo -> sizes [$c1] -> height) {
                            $photo_url = DATA -> object -> message -> attachments [$c] -> photo -> sizes [$c1] -> url;
                            $size = DATA -> object -> message -> attachments [$c] -> photo -> sizes [$c1] -> height;
                        }
                        $c1++;
                    }
                    $urls[$ur] = $photo_url;
                    $ur++;
                }
                $c++;
            }
        }
    }
    if($ur > 0){
        if(get_user_photos() == null){
            $c = 0;
            $str_to_db = '';
            while($c < $ur){
                $str_to_db = $str_to_db.','.$urls[$c];
                $c++;
            }
            $str_to_db = substr($str_to_db, 1);
        } else {
            $str_from_db = get_user_photos();
            $c = 0;
            $str_to_db = '';
            while($c < $ur){
                $str_to_db = $str_to_db.','.$urls[$c];
                $c++;
            }
            $str_to_db = $str_from_db.$str_to_db;
        }
        $stmt = $link->prepare("UPDATE `users` SET `selfie_photos` = ? WHERE `vk_id` = ?");
        if($stmt === false) {
            die ("Mysql Error: " . $link->error);
        }
        $stmt->bind_param('si',  $str_to_db, $from_id);
        $stmt->execute();
        message_send($from_id, "Фото принято. Можно загрузить ещё селфи или завершить регистрацию. ", $keyboards['next_reg']);
        exit();
    } else {
        if (get_user_photos() == null or get_user_photos() == ""){
            message_send($peer_id, "Необходимо загрузить селфи со студенческим.");
        } else {
            message_send($peer_id, "Можно загрузить ещё селфи или продолжить.", $keyboards['next_reg']);
        }
    }
    exit();
}

if(get_user_condition() == "go_to_send"){
    if($payload == "reg_next"){
        set_user_condition("wait_for_moderation"); 
        message_send($peer_id, "Отправили данные модераторам на проверку. Теперь нужно немного подождать.");
    } else {
        $f = json_decode(file_get_contents("faculties.json"));
        message_send($peer_id, "Всё верно?:\nФИО: ".get_user_name("ru")."\nФИО (англ): ".get_user_name("en")."\nФакультет: ".get_user_faculty()."\nКурс: ".$f[get_user_course()]."\nГруппа".get_user_group(), $keyboards['final_check']);
    }
    exit();
}

// МЯСОРУБКА

//
///// user registration end
//
