<?php

namespace SieteSabores\Timeline;

use Desarrolla2\RSSClient\Parser\FeedParser;

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
