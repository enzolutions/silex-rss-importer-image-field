<?php

namespace SieteSabores\Timeline;

use Desarrolla2\RSSClient\Node\Node;
use Desarrolla2\RSSClient\Factory\RSS20NodeFactory;
use Desarrolla2\RSSClient\Parser\FeedParser;

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
