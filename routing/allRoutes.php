<?php

    return array_merge(
        //admin
        include_once (ROOT . '/routing/admin/admin.php'),
        //front
        include_once (ROOT . '/routing/front/ajax.php'),
        include_once (ROOT . '/routing/front/user.php'),
        include_once (ROOT . '/routing/front/main.php')
    );
