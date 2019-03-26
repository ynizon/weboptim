<p align="center"><a href="https://www.gameandme.fr/creation-web/weboptim/" target="_blank"><img width="400" src="https://www.gameandme.fr/wp-content/uploads/2019/03/screenshot_1.png"></a></p>

# **W**eboptim - The Tool for optimization
Made by [Yohann Nizon](ynizon@gmail.com)

![Weboptim Screenshot](https://www.gameandme.fr/wp-content/uploads/2019/03/screenshot_1.png)
![Weboptim Screenshot](https://www.gameandme.fr/wp-content/uploads/2019/03/screenshot_2.png)

Website & Documentation: https://www.gameandme.fr/creation-web/weboptim/

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
APP_URL=http://weboptim.test
```

Note: You need public web server to have the result of the pagespeed optimization.

### 3. Add node js, webpack, gulp and some plugins

Lastly, we need add dependancies. 

To install (you can install dependancies globally (with -g) then link to the project with npm link dependancy_file)

```bash
npm audit fix --force
npm install -g path webpack@latest webpack-dev-server@latest webpack-cli webpack-merge-and-include-globally uglifyjs-webpack-plugin mini-css-extract-plugin css-loader
npm install gulp -g
npm install --save-dev optimize-css-assets-webpack-plugin
npm install -g gulp-imagemin imagemin-guetzli gulp-plumber yargs gulp-uglify
npm install run-sequence gulp-htmlmin gulp-clean-css --save-dev
```

To check install is ok, try this command 

```bash
npm i
webpack -v
gulp -v
```

### 4. Add .htaccess to add server cache (option)

rename public/x.htaccess to public/.htaccess (it depends of your apache config)
Maybe you need to remove some lines
