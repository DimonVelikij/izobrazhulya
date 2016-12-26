<?php

return [
    'admin/login'  =>  [
        'name'      =>  'admin_login',
        'page'      =>  'admin',
        'handler'   =>  'admin/login',
        'title'     =>  'Авторизация админа'
    ],
    'admin/sale'  =>  [
        'name'      =>  'admin_sale',
        'page'      =>  'admin',
        'handler'   =>  'admin/sale',
        'title'     =>  'Продажи'
    ],
    'admin/danger/([0-9]+)'  =>  [
        'name'      =>  'admin_danger',
        'page'      =>  'admin',
        'handler'   =>  'admin/danger/$1',
        'title'     =>  'Отклонено'
    ],
    'admin/success/([0-9]+)'  =>  [
        'name'      =>  'admin_success',
        'page'      =>  'admin',
        'handler'   =>  'admin/success/$1',
        'title'     =>  'Одобрено'
    ],
    'admin/image/([\S])'  =>  [
        'name'      =>  'admin_info',
        'page'      =>  'admin',
        'handler'   =>  'admin/image/$1',
        'title'     =>  'Изображения'
    ],
    'admin'  =>  [
        'name'      =>  'admin',
        'page'      =>  'admin',
        'handler'   =>  'admin/index',
        'title'     =>  'Панель администратора'
    ]
];