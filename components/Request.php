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
     * @var string
     */
    private $title;

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

    /**
     * устанавливаем заголовок
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * получаем заголовок
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * получаем значение сессии по названию
     * @param $name
     * @return null
     */
    public function getSession($name)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return null;
    }

    /**
     * получаем значение get параметра по названию
     * @param $name
     * @return null
     */
    public function get($name)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        return null;
    }
}
