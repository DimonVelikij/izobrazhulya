<?php

return [
    'cabinet/edit/([0-9]+)'  =>  [
        'name'      =>  'cabinet_edit_image',
        'page'      =>  'front',
        'handler'   =>  'user/cabinetEditImage/$1',
        'title'     =>  'Кабинет резактирование изображения'
    ],
    'cabinet/del/([0-9]+)'  =>  [
        'name'      =>  'cabinet_del_image',
        'page'      =>  'front',
        'handler'   =>  'user/cabinetDelImage/$1',
        'title'     =>  'Кабинет удаление изображения'
    ],
    'cabinet'  =>  [
        'name'      =>  'cabinet',
        'page'      =>  'front',
        'handler'   =>  'user/cabinet',
        'title'     =>  'Кабинет пользователя'
    ],
    'logout'  =>  [
        'name'      =>  'logout',
        'page'      =>  'front',
        'handler'   =>  'user/logout',
        'title'     =>  'Выход'
    ],
    'login'  =>  [
        'name'      =>  'login',
        'page'      =>  'front',
        'handler'   =>  'user/login',
        'title'     =>  'Вход'
    ],
    'registration'  =>  [
        'name'      =>  'registration',
        'page'      =>  'front',
        'handler'   =>  'user/registration',
        'title'     =>  'Регистрация'
    ],
];