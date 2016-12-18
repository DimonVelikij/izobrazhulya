<?php

class HomeController extends BaseController
{
    /**
     * главная страница
     * @param Request $request
     * @return bool
     */
    public function indexAction(Request $request)
    {
        return $this->render('home:index.html.twig', [
            'test'  =>  'test',
            'array' =>  [1,2,3]
        ]);
    }
}
