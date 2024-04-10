# Legacy Notes

The following notes were removed from the main README.md file as they are no longer up-to-date, but have been included for historical reference purposes.



## Laravel Auth0 SDK Architecture

> **WARNING**: auth0/login v7.0 introduced **major architectural changes** which were implemented in v2.0 of this library/pattern.  This architectural documentation has NOT BEEN UPDATED.

Compared to other Auth0 PHP code we've seen, Auth0's Laravel library + quickstart introduces an extremely complex (yet flexible) architecture we found very difficult to understand + debug.  We ended up producing a whole set of [documentation](docs/underderstanding-laravel-auth0-authn+authz.md) + [diagrams](docs/laravel-auth0-pattern-diagram.pdf) to help us get our heads around this.  

Hopefully this can be helpful to someone else - whether you're using our library... or simply if you're trying to understand the code from the [Auth0 Laravel Quickstart](https://auth0.com/docs/quickstart/webapp/laravel).

> WARNING: No guarantees are made as to the accuracy of this information.  It was simply our own brain-dump as we tried to decode it all... which then resulted in a number of [diagrams](docs/laravel-auth0-pattern-diagram.pdf) to try to provide a simplified perspective.

> NOTE: the diagrams are the most up-to-date resource.  We didn't try to go back and align our other documentation
 after producing them... OR after RENAMING a few things in the library.

![laravel-auth0-pattern-s2-stucture](images_diagram/laravel-auth0-pattern-s2-stucture.jpg)

## Future Development

> **NOTE**: the remarks in this section may no longer be relevant, since auth0/login v7.0 may have fixed them.  (NOT CHECKED YET)

* During initial development we regularly experienced issues with "Invalid State" errors.  (See our Auth0 Community [support request](https://community.auth0.com/t/handling-laravel-callback-exceptions-invalid-state-and-cant-initialize-a-new-session-while-there-is-one-activ/45103)).  While developing this documentation we discovered that the endless loop for the error *“Can’t initialize a new session while there is one active session already”* can be fixed by the following code - which we executed in a debug session... but haven't yet incorporated into our codebase.

```php
    session()->forget('auth0\_\_user')
```

* In the future, it is anticipated that some variations may be required between projects.  At this time the simplistic cloned/force-publish deploy method for Models will need to be replaced by a more sophisticated approach - ie: using Laravel Traits / parent Classes, etc.

* Auth0's code is not retrieving the 'user_metadata' data during code-exchange.  We have unsuccessfully tried a few things, but moved on to other priorities.  Notes from initial research saved under [documentation / future research](docs/underderstanding-laravel-auth0-authn+authz.md#future-research).

* Could create a migration to remove the (unused) "password_resets" table (as mentioned in the quickstart)... but care needed because this could be destructive if accidentally run against the wrong environment.

