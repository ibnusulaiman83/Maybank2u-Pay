<?php
namespace M2U\Helpers;

class Pay
{

    private $bill;

    public function __construct($urlid)
    {
        global $db;
        $this->bill = $db->get_bill($urlid);
    }

    public function get_bill()
    {
        return $this->bill;
    }
}
