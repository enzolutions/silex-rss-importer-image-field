Silex RSS Importer  Imagen Field
==================

Custom RSS Reader enabled import custom image field + REST Services build with Silex micro-framework

## Demo

If you want to try the output of this REST Server, you can test by yourself at <a target="_blank" href="http://silex.7sabores.com/timeline/rest/covers">http://silex.7sabores.com/timeline/rest/covers</a>.


## Install

### Composer

Execute inside the clone folder the command

````
$ composer install
````

The command above will download silex mini-framework and RSS Client library.

For information about how to install composer go to <a target="_blank"  href="https://github.com/composer/composer">https://github.com/composer/composer</a>

## Dependencies

<small>All installed by Composer</small>

<ul>
  <li>Silex (<a href="http://silex.sensiolabs.org">http://silex.sensiolabs.org</a>)</li>
  <li>RSSClient (<a href="https://github.com/desarrolla2/RSSClient">https://github.com/desarrolla2/RSSClient</a>)</li>
  <li>YamlConfigServiceProvider (<a href="https://github.com/deralex/YamlConfigServiceProvider">https://github.com/deralex/YamlConfigServiceProvider</a>)</li>
  <li>dbal (<a href="https://github.com/doctrine/dbal">https://github.com/doctrine/dbal</a></li>
</ul>

## Features

<ul>
  <li>RSS Importer with Image support</li>
  <li>DB storage for imported items</li>
  <li>REST GET Method to fetch items in JSON format with optional date filters</li>
  <li>Implemented a custom RSS Processor to enable custom imagen field</a>
  <li>Implementd a custom RSS Parses to enable custom image field (not enabled)</li>
</ul>

## Install

Use the file in config/setting.yml.dist to create your own version of setting and name the file settings.yml.

Check the settings sample file
````
database:
    driver: pdo_mysql
    host: localhost
    dbname: time_closeup
    user: root
    password: root
    charset: utf8
rss:
    url: 'http://lightbox.time.com/category/closeup/feed/'

````

<ol>
  <li>Created a DB and put credentials in config/settings.yml</li>
  <li>Configure your webservices to point to web folder as document root</li>
</ol>

For quick test you can go to web folder and run php -S localhost:8000, after that just write localhost:8000 in your browser.

## Usage

RSS Import : Access URL http://localhost:8000/rss/import
REST JSON GET:

  All Items : http://localhost:8000/rest/covers <br/>
  Filters: http://localhost:8000/rest/covers/2014-04-01/2014-04-25

#### Enable CORS

If you are planning use this REST server as source of data for other domains you must to enable the requestor or open for any requestor. The function to acomplish that feature is at the end of web/index.php

````
// Enable CORS
$app->after(function (Request $request, Response $response) {
    //$response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:8081');
});
````
