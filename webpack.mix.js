const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css')
   .postCss('resources/css/notification.css', 'public/css')
   .copy('resources/images', 'public/images');  // Copy images from resources to public
