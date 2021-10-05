<?php


namespace Gzhegow\Router\Tests\Classes;


/**
 * TestUserController
 */
class TestUserController
{
    public function get()
    {
        print_r([ __METHOD__, func_get_args() ]);

        return 1;
    }

    public function post()
    {
        print_r([ __METHOD__, func_get_args() ]);

        return 1;
    }

    public function put()
    {
        print_r([ __METHOD__, func_get_args() ]);

        return 1;
    }

    public function delete()
    {
        print_r([ __METHOD__, func_get_args() ]);

        return 1;
    }


    public function index()
    {
        print_r([ __METHOD__, func_get_args() ]);

        return 1;
    }


    public function login()
    {
        print_r([ __METHOD__, func_get_args() ]);

        return 1;
    }

    public function loginPost()
    {
        print_r([ __METHOD__, func_get_args() ]);

        return 1;
    }


    /**
     * @return array
     */
    public static function controller()
    {
        return [
            'post'  => [ 'POST', 'test/users' ],
            'index' => [ 'GET', 'test/users' ],

            'get'    => [ 'GET', 'test/users/{id}' ],
            'put'    => [ 'PUT', 'test/users/{id}' ],
            'delete' => [ 'DELETE', 'test/users/{id}' ],

            'login'     => [ 'GET', 'test/login' ],
            'loginPost' => [ 'POST', 'test/login' ],
        ];
    }
}
