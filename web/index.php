<?php

require_once __DIR__.'/../vendor/autoload.php';

// Parser libreries
use Desarrolla2\RSSClient\RSSClient;
use SieteSabores\Timeline\TimelinesRSSProcessor;
use SieteSabores\Timeline\FeedParserCustom;

// DB Libraries
use Doctrine\DBAL\Schema\Table;

$app = new Silex\Application();

// Load App YML configuration
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__.'/../config/settings.yml'));

// Set DB Connection
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $app['config']['database']
));

$schema = $app['db']->getSchemaManager();
// Verify DB Table time_covers
if (!$schema->tablesExist('time_covers')) {

  $time_covers = new Table('time_covers');
  $time_covers->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
  $time_covers->setPrimaryKey(array('id'));
  $time_covers->addColumn('title', 'string', array('length' => 128));
  $time_covers->addColumn('link', 'string', array('length' => 255));
  $time_covers->addColumn('thumb', 'string', array('length' => 255));
  $time_covers->addColumn('created', 'datetime');

  $schema->createTable($time_covers);
}

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
          $app['config']['rss']['url'],
      ),
      'time_covers'
  );

  $feeds = $client->fetch('time_covers');

  // Determine the last cover inserted
  $sql = "select created from time_covers order by created desc limit 1";
  $last_cover = $app['db']->fetchAssoc($sql);

  if(!empty($last_cover)) {
    $last_cover_date = new DateTime($last_cover['created']);
  }
  else {
    // Set a old date to import all covers available
    $last_cover_date = new DateTime('1 January 1970 00:00:00');
  }

  $feeds_imported = 0;

  foreach ($feeds as $feed) {
    if($feed->getPubDate()->getTimestamp() > $last_cover_date->getTimestamp() ) {
      $feeds_imported++;
      // Used $feed->getThumb_image(); only with custom parser

      $app['db']->insert('time_covers', array(
        'title' => $feed->getTitle(),
        'link' => $feed->getLink(),
        'thumb' => $feed->getExtended('thumb_image'),
        'created' => $feed->getPubDate()->format('Y-m-d H:i:s')
      ));
    }
  }

  if($feeds_imported) {
    return $app->escape($feeds_imported . ' were imported!');
  } else {
    return $app->escape('No new items were imported');
  }
});

$app->get('/rest/covers/{start_date}/{stop_date}', function ($start_date, $stop_date) use ($app) {

    $sql = "select title, link, thumb, created from time_covers";
    if($start_date && $stop_date) {
      $sql .= " where created between '" . $start_date . "'  and '" . $stop_date . "'";
    }
    $sql .= " order by created desc";

    $results = $app['db']->query($sql);

    $covers = array();

    while ($cover = $results->fetch(PDO::FETCH_NAMED)) {
      $covers[] = $cover;
    }

    return $app->json($covers);
})
->value('start_date', NULL)
->value('stop_date', NULL);;

$app->run();
