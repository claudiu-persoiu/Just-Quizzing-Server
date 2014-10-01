<?php

class Menu
{

    protected $_menuItems = array();

    public function getItems()
    {
        ksort($this->_menuItems);
        return $this->_menuItems;
    }

    public function addItem($name, $callback = false, $ord = false)
    {
        $item = array('name' => $name, 'callback' => $callback);

        if ($ord !== false) {
            $this->_menuItems[$ord] = $item;
        } else {
            $this->_menuItems[] = $item;
        }
    }

    public function count()
    {
        return count($this->_menuItems);
    }
}