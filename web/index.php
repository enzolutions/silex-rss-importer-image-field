<?php

require_once __DIR__.'/../vendor/autoload.php';

use Desarrolla2\RSSClient\RSSClient;
use SieteSabores\Timeline\TimelinesRSSProcessor;
use SieteSabores\Timeline\FeedParserCustom;

$app = new Silex\Application();

$app->get('/rss/import', function() use ($app) {
  $client = new RSSClient();

  // You can choose replace the parser or replace the proccesor
  // Parser add new field in item
  // Processor load the thumb in extended section

  // Custom parser to replace of preprocessor and extend method
  //$parser = new FeedParserCustom();
  //$client->setParser($parser);

  // Custom pushProcessor to force add thumb image
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
    //print_r($feed)
    print_r($feed->getTitle());
    print "<br/>";
    print_r($feed->getLink());
    print "<br/>";
    print_r($feed->getPubDate());
    print "<br/>";
    // Used with or without custom parser
    print_r($feed->getExtended('thumb_image'));
    print "<br/>";

    // Used only with custom parser
    //print_r($feed->getThumb_image());

    print "</pre>";
  }

  return $app->escape('hello');
});

$app['debug'] = true;

$app->run();
