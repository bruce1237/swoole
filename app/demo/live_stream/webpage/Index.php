<?php
namespace webpage;

class Index
{
    public function index()
    {
        return "this is Page(Index), func(Index)\n";
    }

    public function hello(string $id, string $name)
    {
        return "Hello ($id) $name this is Page(Index), func(Hello)\n";
    }
}
