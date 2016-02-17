<?php namespace Jeffsrepoaccount\ResourceAuthorization;

use App\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\AccessDeniedException;
use League\OAuth2\Server\Exception\InvalidRequestException;
use League\OAuth2\Server\ResourceServer;
use Log;


class Middleware
{
    protected $errors;

    public function __construct(
        ResourceServer $server, 
        User $user
    ) {
        $this->server = $server;
        $this->user = $user;
    }

    /**
     * @param Illuminate\Http\Request $request
     * @param Closure $next
     * @return Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if(!$this->server->isValidRequest(true)) {
                return abort(400);
            }

            $this->setUser($request);
        } catch( InvalidRequestException $e ) {
            Log::notice('Bad API Access Attempt, No access token');
            return abort(400);

        } catch( AccessDeniedException $e ) {
            Log::notice('Bad API Access Attempt, Invalid access token', [
                'token' => str_replace('Bearer ', '', $request->header('Authorization')),
            ]);

            return abort(401);
        }

        return $next($request);
    }

    /**
     * Overrides the request's resolver for user()
     *
     * @param Illuminate\Http\Request $request
     */
    protected function setUser($request)
    {
        $userId = $this->server->getAccessToken()->getSession()->getOwnerId(); 
        
        $user = $this->user->findOrFail($userId);
        // Closure will encapsulate preceeding $user object for the 
        // remainder of the request.
        $request->setUserResolver(function() use($user) {
            return $user;
        });
    }
}
