const mix = require('laravel-mix')
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

const webpackConfig = {
  dev: {
    watchOptions: {
      ignored: /node_modules/, // Abaikan folder besar
      aggregateTimeout: 200, // Waktu jeda sebelum build ulang (ms)
      poll: 1000,           // Interval polling (ms)
    },
    parallelism: 4,
    cache: {
      type: 'filesystem'
    }
  },
  production: {
    optimization: {
      splitChunks: {
        chunks: 'all',
      },
      minimize: true
    },
    cache: {
      type: 'filesystem'
    }
  }
}


mix
  .js('resources/js/app.js', 'js/app.js')
  .vue({ version: 2 })
  .webpackConfig(
    Object.assign({
      resolve: {
        alias: {
          '@resources': path.resolve(__dirname, 'resources/'),
          '@': path.resolve(__dirname, 'resources/js/src/'),
          '@themeConfig': path.resolve(__dirname, 'resources/js/themeConfig.js'),
          '@core': path.resolve(__dirname, 'resources/js/src/@core'),
          '@validations': path.resolve(__dirname, 'resources/js/src/@core/utils/validations/validations.js'),
          '@axios': path.resolve(__dirname, 'resources/js/src/libs/axios'),
        },
      },
    }, mix.inProduction() ? webpackConfig.production : webpackConfig.dev),
  )
  .sass('resources/scss/core.scss', 'public/css')
  .options({
    postCss: [require('autoprefixer')],
    processCssUrls: false,
  })
  .copy('resources/css/loader.css', 'public/css')
  .copy('resources/images', 'public/images')
  .extract()
  .sourceMaps(false);


/*
 |--------------------------------------------------------------------------
 | Browsersync Reloading
 |--------------------------------------------------------------------------
 |
 | BrowserSync can automatically monitor your files for changes, and inject your changes into the browser without requiring a manual refresh.
 | You may enable support for this by calling the mix.browserSync() method:
 | Make Sure to run `php artisan serve` and `yarn watch` command to run Browser Sync functionality
 | Refer official documentation for more information: https://laravel.com/docs/9.x/mix#browsersync-reloading
 */
if (mix.inProduction()) {
    mix.version();
} else {
    mix.browserSync('http://localhost:8000/').options({
      watchOptions: {
          ignored: /node_modules/
      }
  });
}
