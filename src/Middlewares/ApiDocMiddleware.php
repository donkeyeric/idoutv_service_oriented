<?php

namespace IdoutvServiceOriented\Middlewares;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoft\Bean\Annotation\Bean;
use Swoft\Middleware\MiddlewareInterface;
use Swoft\Middleware\Http\RouterMiddleware;
use Swoft\App;

/**
 * @Bean()
 * @uses      TestMiddleware
 * @version   2017年11月16日
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class ApiDocMiddleware implements MiddlewareInterface
{
    
    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Interop\Http\Server\RequestHandlerInterface $handler
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $showApiDoc = $request->input('api_doc', false);
        
        if ($showApiDoc) {
            
            $httpHandler = $request->getAttribute(RouterMiddleware::ATTRIBUTE);
            
            list($status, $path, $info) = $httpHandler;
            list($controllerHandler, $matches) = App::getBean('httpHandlerAdapter')->createHandler($path, $info);
            list($controller, $methodName) = $controllerHandler;
            
            $className = get_class($controller);
            
            $response = $handler->handle($request);
            $uriPath = $request->getUri()->getPath();
            
            $apiDocUri = \Swoft\App::$properties['foundation']['ApiDocShowActoinUrl'];
            return $response->redirect("{$apiDocUri}?controller={$className}&action={$methodName}&path={$uriPath}");
            
        } else {
            
            $response = $handler->handle($request);
            return $response;
        }
        
    }
    
}