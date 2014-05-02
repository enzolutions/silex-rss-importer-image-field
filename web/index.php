<?php

require_once __DIR__.'/../vendor/autoload.php';

use Desarrolla2\RSSClient\RSSClient;
use SieteSabores\Timeline\TimelinesRSSProcessor;

$app = new Silex\Application();

$app->get('/rss/import', function() use ($app) {
  $client = new RSSClient();

  $client->pushProcessor( new TimelinesRSSProcessor($client->getSanitizer()));


  $client->addFeeds(
      array(
          'http://lightbox.time.com/category/closeup/feed/',
      ),
      'time_covers'
  );

  $feeds = $client->fetch('time_covers');

  foreach ($feeds as $feed) {
    print "<pre>";
    print_r($feed->getTitle());
    print_r($feed->getLink());
    print "<br/>";
    print_r($feed->getPubDate());
    print_r($feed->getExtended('thumb_image'));
    print "</pre>";
  }

  return $app->escape('hello');
});

$app['debug'] = true;

$app->run();
