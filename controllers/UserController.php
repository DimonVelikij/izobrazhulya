<?php

class UserController extends BaseController
{
    /**
     * регистрация
     * @param Request $request
     * @return bool
     */
    public function registrationAction(Request $request)
    {
        $f_name = '';
        $s_name = '';
        $login = '';
        $password = '';
        $errors = false;

        if (isset($_POST['registration'])) {
            $f_name = $_POST['f_name'];
            $s_name = $_POST['s_name'];
            $login = $_POST['login'];
            $password = $_POST['password'];

            if (!Validation::isEmpty($f_name)) {
                $errors[] = 'Заполните поле "Фамилия"';
            }
            if (!Validation::isEmpty($s_name)) {
                $errors[] = 'Заполните поле "Имя"';
            }
            if (!Validation::isLogin($login)) {
                $errors[] = 'Неверно заполнено поле "Логин"';
            }
            if (!Validation::isEmpty($password)) {
                $errors[] = 'Заполните поле "Пароль"';
            }
            if (!$errors) {
                $user = User::registration($f_name, $s_name, $login, $password);
                if ($user) {
                    User::setSessionUser($user['id']);
                    header('Location:/cabinet');
                }
                $errors[] = 'Произошла ошибка регистрации';
            }
        }

        return $this->render('user:registration.html.twig', [
            'f_name'    =>  $f_name,
            's_name'    =>  $s_name,
            'login'     =>  $login,
            'password'  =>  $password,
            'errors'    =>  $errors
        ]);
    }

    /**
     * вход
     * @param Request $request
     * @return bool
     */
    public function loginAction(Request $request)
    {
        if (User::getSessionUser()) {
            header('Location:/');
        }
        $login = '';
        $password = '';
        $errors = false;

        if (isset($_POST['enter'])) {
            $login = $_POST['login'];
            $password = $_POST['password'];

            $user = User::login($login, $password);
            if ($user) {
                User::setSessionUser($user['id']);
                header('Location:/cabinet');
            }
            $errors[] = 'Неверный логин или пароль';
        }

        return $this->render('user:login.html.twig', [
            'login'     =>  $login,
            'password'  =>  $password,
            'errors'    =>  $errors
        ]);
    }

    /**
     * route(/logout)
     * @param Request $request
     * @return bool
     */
    public function logoutAction(Request $request)
    {
        User::delSessionUser();
        header("Location:/");
    }

    /**
     * route(/cabinet)
     * @param Request $request
     * @return bool
     */
    public function cabinetAction(Request $request)
    {
        $price_large = '';
        $price_middle = '';
        $price_little = '';
        $hash_tags = '';

        $errors = false;

        if (isset($_POST['add-image'])) {
            $price_large = $_POST['price-large'];
            $price_middle = $_POST['price-middle'];
            $price_little = $_POST['price-little'];
            $hash_tags = $_POST['hash-tags'];

            if (!Validation::isNumber($price_large)) {
                $errors[] = 'Неверно указана цена для большого изображения';
            }
            if (!Validation::isNumber($price_middle)) {
                $errors[] = 'Неверно указана цена для среднего изображения';
            }
            if (!Validation::isNumber($price_little)) {
                $errors[] = 'Неверно указана цена для маленького изображения';
            }
            if (!Validation::isEmpty($hash_tags)) {
                $errors[] = 'Заполните поле "Хеш-теги"';
            }
            if ($errors === false) {
                $price = new Price($price_large, $price_middle, $price_little);
                $upload = $this->upload($price);
                if (is_int($upload)) {
                    $hash_tags_array = explode(',', $hash_tags);
                    if (count($hash_tags_array)) {
                        for ($i = 0; $i < count($hash_tags_array); $i++) {
                            HashTags::addHashTag($upload, trim($hash_tags_array[$i]));
                        }
                    }
                    header('Location: /cabinet');
                } else {
                    $errors[] = $upload;
                }
            }
        }

        return $this->render('user:cabinet.html.twig', [
            'price_large'   =>  $price_large,
            'price_middle'  =>  $price_middle,
            'price_little'  =>  $price_little,
            'hash_tags'     =>  $hash_tags,
            'images'        =>  Picture::getImagesByUserId(User::getSessionUser()),
            'errors'        =>  $errors
        ]);
    }

    /**
     * Удаление директории с изображениями
     * @param $dir
     */
    private function delImageDir($dir)
    {
        if ($objects = glob($dir . '/*')) {
            foreach ($objects as $object) {
                is_dir($object) ? $this->delImageDir($object) : unlink($object);
            }
        }
        rmdir($dir);
    }
    /**
     * удаление изображения
     * @param Request $request
     * @param $image_id
     */
    public function cabinetDelImageAction(Request $request, $image_id)
    {
        $this->delImageDir(ROOT . '/media/' . $image_id);
        Picture::removeImage($image_id);
        header("Location:/cabinet");
    }

    public function cabinetEditImageAction(Request $request, $image_id)
    {
        $image = Picture::getImageById($image_id);

        $price_large = $image['large'];
        $price_middle = $image['middle'];
        $price_little = $image['little'];
        $hash_tags = $image['hash_tags'];

        $errors = false;

        if (isset($_POST['edit-image'])) {
            $price_large = $_POST['price-large'];
            $price_middle = $_POST['price-middle'];
            $price_little = $_POST['price-little'];
            $hash_tags = $_POST['hash-tags'];

            if (!Validation::isNumber($price_large)) {
                $errors[] = 'Неверно указана цена для большого изображения';
            }
            if (!Validation::isNumber($price_middle)) {
                $errors[] = 'Неверно указана цена для среднего изображения';
            }
            if (!Validation::isNumber($price_little)) {
                $errors[] = 'Неверно указана цена для маленького изображения';
            }
            if (!Validation::isEmpty($hash_tags)) {
                $errors[] = 'Заполните поле "Хеш-теги"';
            }

            if ($errors === false) {
                $price_sizes = [$price_large, $price_middle, $price_little];
                HashTags::delHashTags($image_id);
                $hash_tags_array = explode(',', $hash_tags);
                if (count($hash_tags_array)) {
                    for ($i = 0; $i < count($hash_tags_array); $i++) {
                        HashTags::addHashTag($image_id, trim($hash_tags_array[$i]));
                    }
                }
                $sizes = Size::getSizesByImageId($image_id);
                $i = 0;
                foreach ($sizes as $size) {
                    for ($j = 0; $j < count($size); $j++) {
                        Size::updateSizesPriceByImageId($image_id, $size[$j], $price_sizes[$i]);
                    }
                    $i++;
                }
                header("Location:/cabinet/edit/$image_id");
            }
        }

        return $this->render('user:cabinet-edit-image.html.twig', [
            'image' =>  $image_id,
            'price_large'   =>  $price_large,
            'price_middle'  =>  $price_middle,
            'price_little'  =>  $price_little,
            'hash_tags'     =>  $hash_tags
        ]);
    }
}
