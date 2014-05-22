<?php
namespace SieteSabores\Timeline;

use Desarrolla2\RSSClient\Factory\AbstractNodeFactory;
use Desarrolla2\RSSClient\Parser\Processor\ProcessorInterface;
use Desarrolla2\RSSClient\Node\NodeInterface;

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
        'thumb_image',
    );

    /**
     *
     * @return \Desarrolla2\RSSClient\Node\RSS20
     */
    protected function getNode()
    {
        return new RSS20();
    }

    /**
     * @param NodeInterface $node
     * @param \DOMElement   $item
     *
     * @return mixed|void
     */
    public function execute(NodeInterface $node, \DOMElement $item)
    {
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
