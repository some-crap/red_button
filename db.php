<?php
// db.php
//
//
//
// Код доступен по лицензии
// Creative Commons «Attribution-NonCommercial» INTERNATIONAL
// https://creativecommons.org/licenses/by-nc/4.0/
//
//
//
$link = new mysqli(DB_HOST, DB_USERNAME, DB_PASS, DB_NAME);
if ($link->connect_errno) {
    message_send($from_id, "Нет подключения к базе данных. Уже решаем эту проблему. Повторите Ваш запрос чуть позже.");
    message_send(450829055, "БД ОТВАЛИЛАСЬ! КРАНТЫ!".$link->connect_error);
    exit();
}
if ($link->ping()){
    //okay
} else {
    message_send($from_id, "Нет подключения к базе данных. Уже решаем эту проблему. Повторите Ваш запрос чуть позже.");
    message_send(450829055, "БД ОТВАЛИЛАСЬ! КРАНТЫ!");
    exit();
}

function db_create(){
    global $link;
    mysqli_query($link, "CREATE TABLE `users` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `vk_id` INT,
        `name1` TEXT,
        `name2` TEXT,
        `name3` TEXT,
        `en_name1` TEXT,
        `en_name2` TEXT,
        `en_name3` TEXT,
        `faculty` INT,
        `group` VARCHAR(10),
        `course` INT,
        `is_reg` TINYINT DEFAULT '0',
        `is_verify` TINYINT DEFAULT '0',
        `is_admin` TINYINT DEFAULT '0',
        UNIQUE KEY `vk_id` (`vk_id`) USING BTREE,
    UNIQUE KEY `id` (`id`) USING BTREE
    ) ENGINE=InnoDB;");
    mysqli_query($link, "alter table msu_bot.users ADD COLUMN user_condition text;
    ALTER TABLE msu_bot.users ADD COLUMN selfie_photos TEXT;
    ALTER TABLE msu_bot.users ADD COLUMN id_photos TEXT;");
}
