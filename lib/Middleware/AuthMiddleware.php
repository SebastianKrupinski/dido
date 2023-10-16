<?php

namespace OCA\Data\Middleware;

use \OCP\AppFramework\Middleware;

class AuthMiddleware extends Middleware {

    public function beforeController($controller, $methodName) {
        return 'blahblahblah';
    }

}