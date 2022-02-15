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
// keyboards.php

$keyboards = array(
    "accept" => '{
        "one_time": true,
        "buttons": [
        [
        {
        "action": {
        "type": "text",
        "label": "Ознакомился, согласен и принимаю",
        "payload": "{\"button\": \"accept_rules\"}"
        },
        "color": "primary"
        }
        ]
        ]
        }',

        "change_reg" => '{
            "one_time": true,
            "buttons": [
                [
                    {
                    "action": {
                    "type": "text",
                    "label": "Редактировать",
                    "payload": "{\"button\": \"reg_edit\"}"
                    },
                    "color": "primary"
                    }
                ,
                
                    {
                    "action": {
                    "type": "text",
                    "label": "Верно",
                    "payload": "{\"button\": \"reg_next\"}"
                    },
                    "color": "primary"
                    }
                ]
            ]
            }',

            "reg_select_course" => '{
                "one_time": true,
                "buttons": [
                    [
                        {
                        "action": {
                        "type": "text",
                        "label": "1 бак/спец",
                        "payload": "{\"button\": \"course_1\"}"
                        },
                        "color": "primary"
                        }
                    ,
                    
                        {
                        "action": {
                        "type": "text",
                        "label": "2 бак/спец",
                        "payload": "{\"button\": \"course_2\"}"
                        },
                        "color": "primary"
                        }
                    ],
                    [
                        {
                        "action": {
                        "type": "text",
                        "label": "3 бак/спец",
                        "payload": "{\"button\": \"course_3\"}"
                        },
                        "color": "primary"
                        }
                    ,
                    
                        {
                        "action": {
                        "type": "text",
                        "label": "4 бак/спец",
                        "payload": "{\"button\": \"course_4\"}"
                        },
                        "color": "primary"
                        }
                    ],
                    [
                        {
                        "action": {
                        "type": "text",
                        "label": "5 спец/ 1 Маг",
                        "payload": "{\"button\": \"course_5\"}"
                        },
                        "color": "primary"
                        }
                    ,
                    
                        {
                        "action": {
                        "type": "text",
                        "label": "6 спец/ 2 Маг",
                        "payload": "{\"button\": \"course_6\"}"
                        },
                        "color": "primary"
                        }
                    ]
                ]
                }',
                "next_reg" => '{
                    "one_time": true,
                    "buttons": [
                        [
                            {
                            "action": {
                            "type": "text",
                            "label": "Дальше",
                            "payload": "{\"button\": \"reg_next\"}"
                            },
                            "color": "primary"
                            }
                        ]
                    ]
                    }',
                    
                    "reg_send" => '{
                        "one_time": true,
                        "buttons": [
                            [
                                {
                                "action": {
                                "type": "text",
                                "label": "Отправить",
                                "payload": "{\"button\": \"reg_next\"}"
                                },
                                "color": "primary"
                                }
                            ]
                        ]
                        }'
    );
