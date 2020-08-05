const mix = require('laravel-mix');
require('mix-env-file');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.react('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');

// Copy into dist folder for production
if (mix.inProduction()) {
    mix.env(process.env.ENV_FILE);
    
	// App
	mix.copyDirectory('app', 'dist/cinema/app');
	mix.copyDirectory('config', 'dist/cinema/config');
	mix.copyDirectory('database', 'dist/cinema/database');
	mix.copyDirectory('resources/views', 'dist/cinema/resources/views');
	mix.copyDirectory('routes', 'dist/cinema/routes');
    mix.copyDirectory('storage/app/public', 'dist/cinema/storage/app/public');
	mix.copyDirectory('tests', 'dist/cinema/tests');

	// Public
	mix.copyDirectory('public/css', 'dist/cinema.angelin.dev/css');
	mix.copyDirectory('public/js', 'dist/cinema.angelin.dev/js');
}