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
        $this->upload();
        
        return $this->render('user:cabinet.html.twig', [

        ]);
    }
}
