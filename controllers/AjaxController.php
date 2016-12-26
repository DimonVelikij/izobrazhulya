<?php

class AjaxController extends BaseController
{
    /**
     * @param Request $request
     * @return int
     */
    public function imagesAction(Request $request)
    {
        $images = Picture::getImageByStatus('success');

        echo json_encode([
            'images'    =>  $images
        ]);
        return true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function requestAction(Request $request)
    {
        $image_id = $_GET['image_id'];
        $email = $_GET['email'];
        $cart = $_GET['cart'];
        $price1 = isset($_GET['price1']) ? $_GET['price1'] : null;
        $price2 = isset($_GET['price2']) ? $_GET['price2'] : null;
        $price3 = isset($_GET['price3']) ? $_GET['price3'] : null;

        $total_price = 0;
        if ($price1) {
            $total_price += $price1;
        }
        if ($price2) {
            $total_price += $price2;
        }
        if ($price3) {
            $total_price += $price3;
        }

        $result = Sale::addSale($image_id, $total_price);

        $theme = 'Вы купили изображения';
        $msg = 'message';
        $mail = mail($email, $theme, $msg);

        echo json_encode([
            'mail'      =>  $mail,
            'success'   =>  $result
        ]);

        return true;
    }
}