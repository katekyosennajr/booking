const mix = require('laravel-mix');

mix.js('public/js/vue/app.js', 'public/js/app.js')
   .vue()
   .sass('public/css/app.scss', 'public/css');
