<?php

class AdminController extends AdminBaseController
{
    /**
     * @param Request $request
     * @return bool
     */
    public function loginAction(Request $request)
    {
        $login = '';
        $password = '';

        $errors = [];

        if(isset($_POST['admin-login'])) {

            $login = $_POST['login'];
            $password = $_POST['password'];

            $errors = false;

            //if admin exist
            if(!Admin::getAdmin($login,$password)) {
                $errors[] = 'Неверный логин или пароль';
            }

            if($errors === false) {
                $rand = Admin::getRand();
                if($rand && Admin::setUserAdminRand($rand)) {
                    Admin::setSessionUserAdmin($rand);
                }
                header('Location:/admin/');
            }
        }

        return $this->render('user:login.html.twig', [
            'login'     =>  $login,
            'password'  =>  $password,
            'errors'    =>  $errors
        ]);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function indexAction(Request $request)
    {
        $images = Picture::getImages();

        return $this->render('main:index.html.twig', [
            'caption'   =>  'Коллекция изображений',
            'images'    =>  $images
        ]);
    }

    /**
     * @param Request $request
     * @param $status
     * @return bool
     */
    public function imageAction(Request $request, $status)
    {
        $images = Picture::getImageByStatus($status);
        $status = Status::getStatusByAlias($status);

        return $this->render('main:index.html.twig', [
            'caption'   =>  $status['title'],
            'images'    =>  $images
        ]);
    }

    /**
     * @param Request $request
     * @param $image_id
     */
    public function successAction(Request $request, $image_id)
    {
        $status = Status::getStatusByAlias('success');
        Picture::updateImageStatus($image_id, $status['id']);
        header("Location:" . $_SERVER['HTTP_REFERER']);
    }

    /**
     * @param Request $request
     * @param $image_id
     */
    public function dangerAction(Request $request, $image_id)
    {
        $status = Status::getStatusByAlias('danger');
        Picture::updateImageStatus($image_id, $status['id']);
        header("Location:" . $_SERVER['HTTP_REFERER']);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function saleAction(Request $request)
    {
        $sale_list = Sale::getSaleList();
        $total_price = 0;
        if (count($sale_list)) {
            foreach ($sale_list as $image_id => $sale) {
                $total_price += $sale['total_price'];
            }
        }

        return $this->render('main:sale.html.twig', [
            'total_price'   =>  $total_price,
            'sale_list'     =>  $sale_list
        ]);
    }
}