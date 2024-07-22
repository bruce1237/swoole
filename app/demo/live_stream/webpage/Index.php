<?php
namespace webpage;

class Index
{
    public function index()
    {
        if (!Login::isLogin()) {
            return "GO TO LOGIN";
        }

        return "Index/Index";
    }

    
    
}
