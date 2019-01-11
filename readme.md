## About libraries and frameworks
##### Laravel application:
- Laravel framework used to build REST API (Players and Teams)
- VueJs used to build SPA front-end application

- Other back-end libraries: jwt-auth (for jwt token management and authorization)
- Other front-end libraries: vuex, vue-router, bootstrap-vue, vue-loading-overlay, toastr

### Quick start

- create an empty database and set up the .env file with proper connection details
- run `composer install` (composer dependencies install)
- run `php artisan migrate --seed` (run migrations and seeders)
- run `npm install` (npm dependencies install)
- run `npm run dev` (dev assets build) (or `npm run production` (production build), or `npm run watch` (dev build + watch))
- run `php -S 0.0.0.0:8080 -t public` to serve the application under a local php builtin server


### Temporary Demo URL:

`http://213.32.71.136:8001`

Default login credentials: `johndoe@gmail.com:123456`
