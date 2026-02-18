let mix = require('laravel-mix')
let path = require('path')
let NovaExtension = require('laravel-nova-devtool')

mix.extend('nova', new NovaExtension())

mix
  .setPublicPath('dist')
  .js('resources/js/tool.js', 'js')
  .vue({ version: 3 })
  .css('resources/css/tool.css', 'css')
  .nova('opscale-co/nova-toolbar-actions')
  .alias({
    '@': path.resolve(__dirname, 'vendor/laravel/nova/resources/js/'),
  })
  .version()
