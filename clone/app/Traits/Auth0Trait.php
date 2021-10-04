<?php
/**
 * This file is cloned / force-published from the "auth-laravel-v1" composer package.
 *    WARNING: Local modifications will be overwritten when the package is updated.
 *             See https://github.com/faithfm/auth-laravel-v1 for more details.
 */

// This Trait can be applied to the User model... extending it to include Auth0 authentication information.
// It works in conjunction with our CustomUserRepository.php implementation.
//
// Note: CustomUserRepository.php was initially based on Auth0's Laravel Quickstart's "Optional: Custom User Handling" section...   (See May 2020 version of https://auth0.com/docs/quickstart/webapp/laravel#optional-custom-user-handling)
//      ...but the tutorial had errors - see https://github.com/auth0/docs/issues/9002
//      ...and even once the errors were corrected, it returned an Auth0User interface (with a copy of the static "getAttributes()" properties from the User model.  (No access to User methods, etc)
//      Apart from completely changing the way that User data can be accessed for the currently-authenticated user, the Auth0User class is not compatible with other standard features including Laravel's guards implementation.
//      Note: Auth0 are currently looking to rectify this by switching their implementation to use an "Auth0 Trait" (to extend the normal User model instead).  (See https://github.com/auth0/laravel-auth0/pull/165)
//      However until this becomes generally available, I've adapted @aaronflorey's code to create our own Auth0Trait which we add to the User model.   (See https://gist.github.com/aaronflorey/d20f27a2b0475d238e10b46de3bc3eb4)

namespace App\Traits;

trait Auth0Trait
{
	public $accessToken;

	/**
	 * Get id field name.
	 *
	 * @return string
	 */
	public function getAuthIdentifierName()
	{
		return 'id';
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->accessToken;
	}

	/**
	 * @return mixed
	 */
	public function getAccessToken(): ?string
	{
		return $this->accessToken;
	}

	/**
	 * @param string $token
	 * @return $this
	 */
	public function setAccessToken(string $token): self
	{
		$this->accessToken = $token;
		return $this;
	}
}
