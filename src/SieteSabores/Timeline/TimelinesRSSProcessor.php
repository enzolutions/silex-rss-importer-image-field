<?php
namespace SieteSabores\Timeline;

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
