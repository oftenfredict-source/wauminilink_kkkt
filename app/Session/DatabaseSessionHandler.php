<?php

namespace App\Session;

use Illuminate\Session\DatabaseSessionHandler as BaseHandler;
use Illuminate\Support\Facades\Auth;

class DatabaseSessionHandler extends BaseHandler
{
    /**
     * Get the default payload for the session.
     * Override to ensure user_id is always set when user is authenticated
     *
     * @param  string  $data
     * @return array
     */
    protected function getDefaultPayload($data)
    {
        $payload = parent::getDefaultPayload($data);
        
        // Ensure user_id is set if user is authenticated
        // This works even if Guard is not bound in container
        if (Auth::check() && (!isset($payload['user_id']) || !$payload['user_id'])) {
            $payload['user_id'] = Auth::id();
        }
        
        return $payload;
    }
}

