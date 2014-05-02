<?php

namespace SieteSabores\Timeline;

use \DOMElement;
use Desarrolla2\RSSClient\Factory\RSS20NodeFactory;

/**
 * RSS20NodeFactoryCustom
 *
 */
class RSS20NodeFactoryCustom extends RSS20NodeFactory
{

  /**
   *
   * @return \SieteSabores\Timeline\RSS20Custom
   */
  protected function getNode()
  {
      return new RSS20Custom();
  }

  /**
   * @param DOMElement $item
   * @param RSS20Custom $node
   */
  protected function setCategories(DOMElement $item, RSS20Custom $node)
  {
      $categories = $this->getNodeValuesByTagName($item, 'category');
      foreach ($categories as $category) {
          $node->addCategory(
              $this->doClean($category)
          );
      }
  }

  /**
   * @param DOMElement $item
   * @param RSS20Custom $node
   */
  protected function setLink(DOMElement $item, RSS20Custom $node)
  {
      $value = $this->getNodeValueByTagName($item, 'link');
      if ($this->isValidURL($value)) {
          $node->setLink(
              $this->doClean($value)
          );
      }
  }

  /**
   * @param DOMElement                        $item
   * @param \SieteSabores\Timeline\RSS20Custom $node
   * @param RSS20Custom                        $node
   */
  protected function setPubDate(DOMElement $item, RSS20Custom $node)
  {
      $value = $this->getNodeValueByTagName($item, 'pubDate');
      if ($value) {
          if (strtotime($value)) {
              $node->setPubDate(new \DateTime($value));
          }
      }
  }

  /**
   * @param DOMElement $item
   * @param RSS20Custom $node
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
              //print $method . "<br/>";
              $node->$method(
                  $this->doClean($value)
              );
          }
      }
  }
}
