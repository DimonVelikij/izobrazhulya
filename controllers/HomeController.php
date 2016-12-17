<?php

class HomeController extends BaseController
{
    public function indexAction(Request $request)
    {
        return $this->render('home:index.html.twig', [
            'test'  =>  'test'
        ]);
    }
}