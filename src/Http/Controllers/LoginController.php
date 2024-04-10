<?php

declare(strict_types=1);

namespace FaithFM\Auth0Pattern\Http\Controllers;

use Auth0\Laravel\Controllers\LoginControllerAbstract;
use Auth0\Laravel\Controllers\LoginControllerContract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for handling a login request.
 *
 * @api
 */
final class LoginController extends LoginControllerAbstract implements LoginControllerContract
{

    /**
     * Capture the 'previous' URL and save it as the 'intended' URL...
     *       (so the callback route will redirect us back there after a successful login)
     * ...before calling parent (Auth0) login controller
     * 
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @param Request $request
     */
    public function __invoke(Request $request): Response {

        // Capture current and previous URLs
        $current = url()->current();
        $previous = url()->previous();

        // Compare URLs to ensure we don't create an endless loop redirecting back to the login page
        if ($current !== $previous) {
            // Set the 'intended' URL to the 'previous' URL (so the '/callback' route will redirect us back there after a successful login)
            redirect()->setIntendedUrl($previous);
        }

        return parent::__invoke($request);
    }

}
