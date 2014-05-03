time-closeup-silex
==================

Custom RSS Reader + REST Services build with Silex micro-framework

## Install

### Composer

Execute inside the clone folder the command

````
$ composer install
````

The command above will download silex mini-framework and RSS Client library.

For information about how to install composer go to <a href="https://github.com/composer/composer">https://github.com/composer/composer</a>

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
</ul>

## Install

<ol>
  <li>Created a DB and put credentials in config/settings.yml</li>
  <li>Configure your webservices to point to web folder as document root</li>
</ol>

For quick test you can go to web folder and run php -S localhost:8000, after that just write localhost:8000 in your browser.

## Usage

RSS Import : Access URL http://localhost:8000/rss/import
REST JSON GET:

  All Items : http://localhost:8000/rest/covers
  Filters: http://localhost:8000/rest/covers/2014-04-01/2014-04-25
