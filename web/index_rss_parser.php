<?php

require_once __DIR__.'/../vendor/autoload.php';

use Desarrolla2\RSSClient\RSSClient;
use Desarrolla2\RSSClient\Parser\Processor\ProcessorInterface;
use Desarrolla2\RSSClient\Factory\AbstractNodeFactory;
use Desarrolla2\RSSClient\Node\NodeInterface;
use Desarrolla2\RSSClient\Factory\RSS20NodeFactory;
use Desarrolla2\RSSClient\Parser\FeedParser;
use Desarrolla2\RSSClient\Node\Node;

/**
 *
 * RSS20
 *
 */
class RSS20Custom extends Node
{
  /**
     * @var string
     */
    protected $thumb_image = null;

  /**
   * @param DOMElement $item
   * @param RSS20      $node
   */
  public function setthumb_image($thumb_image)
    {
        $this->thumb_image = $thumb_image;
    }
}

/**
 * RSS20NodeFactoryCustom
 *
 */
class RSS20NodeFactoryCustom extends RSS20NodeFactory
{

  /**
   *
   * @return \Desarrolla2\RSSClient\Node\RSS20
   */
  protected function getNode()
  {
      return new RSS20Custom();
  }

  /**
   * @param DOMElement $entry
   * @param Atom10     $node
   */
  protected function setCategories(DOMElement $entry, RSS20Custom $node)
  {
      $categories = $this->getNodePropertiesByTagName($entry, 'category', 'term');
      foreach ($categories as $category) {
          $node->addCategory(
              $this->doClean($category)
          );
      }
  }

  /**
   * @param DOMElement $entry
   * @param Atom10     $node
   * @throws \Desarrolla2\RSSClient\Exception\ParseException
   */
  protected function setLink(DOMElement $entry, RSS20Custom $node)
  {
      try {
          $results = $entry->getElementsByTagName('link');
          if ($results->length) {
              foreach ($results as $result) {
                  if ($result->getAttribute('rel') == 'alternate') {
                      $value = $result->getAttribute('href');
                      if ($this->isValidURL($value)) {
                          $node->setLink(
                              $this->doClean($value)
                          );

                          return;
                      }
                  }
              }
          }
      } catch (\Exception $e) {
          throw new ParseException($e->getMessage());
      }
  }

  /**
   * @param DOMElement $entry
   * @param Atom10     $node
   */
  protected function setPubDate(DOMElement $entry, RSS20Custom $node)
  {
      $value = $this->getNodeValueByTagName($entry, 'published');
      if ($value) {
          if (strtotime($value)) {
              $node->setPubDate(new DateTime($value));
          }
      }
  }

  /**
   * @param DOMElement $item
   * @param RSS20      $node
   */
  protected function setProperties(DOMElement $item, RSS20Custom $node)
  {
      $properties = array(
          'title',
          'description',
          'author',
          'comments',
          'enclosure',
          'guid',
          'source',
          'thumb_image'
      );
      foreach ($properties as $propertyName) {
          $value = $this->getNodeValueByTagName($item, $propertyName);
          if ($value) {
              $method = 'set' . $propertyName;
              $node->$method(
                  $this->doClean($value)
              );
          }
      }
  }
}
/**
 *
 * FeedParserCustom
 *
 */
class FeedParserCustom extends FeedParser {
  /**
   *  Parse Feed and create a node collection
   *
   * @param  string $feed
   *
   * @return array
   * @throws ParseException
   */
  public function parse($feed)
  {
      $domDocument = $this->createDomDocument($feed);

      switch ($this->getSchema($domDocument)) {
          case 'RSS20':
              $nodes = $this->parseWithFactory(
                  new RSS20NodeFactoryCustom($this->sanitizer),
                  $domDocument,
                  'item'
              );
              break;
          case 'ATOM10':
              $nodes = $this->parseWithFactory(
                  new Atom10NodeFactory($this->sanitizer),
                  $domDocument,
                  'entry'
              );
              break;
          default:
              throw new ParseException('Schema not supported');
              break;
      }

      return $nodes;

  }
}

class TimelinesRSSProcessor extends AbstractNodeFactory implements ProcessorInterface
{
    /**
     * @var array
     */
    protected $mediaTypes = array(
        'content',
        'keywords',
        'thumbnail',
        'category',
        'comments',
    );

    /**
     *
     * @return \Desarrolla2\RSSClient\Node\RSS20
     */
    protected function getNode()
    {
        return new RSS20Custom();
    }

    /**
     * @param NodeInterface $node
     * @param \DOMElement   $item
     *
     * @return mixed|void
     */
    public function execute(NodeInterface $node, \DOMElement $item)
    {
        print "<pre>";
        print_r($node);
        print "</pre>";
        die();
        foreach ($this->mediaTypes as $mediaType) {
            /* Implement getNodeValueByTagName yourself */
            $value = $this->getNodeValueByTagName($item, $mediaType);
            if ($value) {
                $node->setExtended(
                    $mediaType,
                    $value
                );

            }
        }
    }
}

$app = new Silex\Application();

$app->get('/rss/import', function() use ($app) {
  $client = new RSSClient();
  $parser = new FeedParserCustom();

  $client->setParser($parser);
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
    print_r($feed);
    print "</pre>";
  }

  return $app->escape('hello');
});

$app['debug'] = true;

$app->run();
