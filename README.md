<p align="center"><a href="https://www.gameandme.fr/weboptim/" target="_blank"><img width="400" src="https://s3.amazonaws.com/thecontrolgroup/voyager.png"></a></p>

<p align="center">
<a href="https://packagist.org/packages/tcg/voyager"><img src="https://poser.pugx.org/tcg/voyager/v/stable.svg?format=flat" alt="Latest Stable Version"></a>
</p>

# **W**eboptim - The Tool for optimization
Made by [Yohann Nizon](ynizon@gmail.com)

![Weboptim Screenshot](https://www.gameandme.fr/weboptim/weboptim.png)

Website & Documentation: https://www.gameandme.fr/weboptim

<hr>

## Installation Steps

### 1. Require some PHP Package

```bash
composer update
```

### 2. Add your Pagespeed API & APP_URL...

Next add your OS and Pagespeed API key to your .env file:

```
APP_NAME=Weboptim
APP_ENV=local
PAGESPEED_API = 
```

You will also want to update your website URL inside of the `APP_URL` variable inside the .env file:

```
APP_URL=http://localhost:8000
```

Note: You need public web server to have the result of the pagespeed optimization.

### 3. Add node js, webpack, gulp and some plugins

Lastly, we need add dependancies. 

To install 

```bash
npm install -g path webpack@latest webpack-dev-server@latest webpack-cli webpack-merge-and-include-globally
npm install gulp -g
npm install --save-dev optimize-css-assets-webpack-plugin
npm install -g gulp-imagemin imagemin-guetzli gulp-plumber yargs gulp-uglify
npm install run-sequence gulp-htmlmin gulp-clean-css --save-dev
```

To try install is ok 

```bash
npm i
webpack
```

### 4. Add .htaccess to add server cache (option)

rename public/x.htaccess to public/.htaccess (it depends of your apache config)
Maybe you need to remove some lines