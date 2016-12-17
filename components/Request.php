<?php

class Request
{
    /**
     * @var string
     */
    private $route_name;

    /**
     * @var string
     */
    private $page;

    /**
     * устанавливаем название роута
     * @param $route_name
     */
    public function setRouteName($route_name)
    {
        $this->route_name = $route_name;
    }

    /**
     * получаем название роута
     * @return string
     */
    public function getRouteName()
    {
        return $this->route_name;
    }

    /**
     * устанавливаем название страницы
     * @param $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * получаем название страницы
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }
}
