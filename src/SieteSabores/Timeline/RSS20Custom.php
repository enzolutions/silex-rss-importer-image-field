<?php

namespace SieteSabores\Timeline;

use \DOMElement;
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
   * @param $thumb_image
   *
   * @return mixed|void
   */
  public function setthumb_image($thumb_image)
  {
    $this->thumb_image = $thumb_image;
  }

  /**
   * @param $thumb_image
   *
   * @return mixed|void
   */
  public function getThumb_image()
  {
    return $this->thumb_image;
  }
}
