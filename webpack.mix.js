const mix = require('laravel-mix');

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

mix.autoload({
    // 'jquery': ['$', 'window.jQuery', 'jQuery'],
    // 'vue': ['Vue','window.Vue'],
    // 'moment': ['moment','window.moment'],
});
// mix.js('resources/js/main.logistic.map.js', 'public/js');
// mix.js('resources/js/implement.map.js', 'public/js');
// mix.js('resources/js/get.address.data.js', 'public/js');
// mix.js('resources/js/logistic.routes.map.js', 'public/js');
// mix.js('resources/js/lid.js', 'public/js');
// mix.js('resources/js/calc.pest.js', 'public/js');
// mix.js('resources/js/new.acts.js', 'public/js');
// mix.js('resources/js/logistic.routes.js', 'public/js');
mix.js('resources/js/pusher.js', 'public/js');
    // .sass('resources/sass/app.scss', 'public/css');
