<?php

namespace VCR\VCRBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VCR\VCREvents;
use VCR\Event\AfterPlaybackEvent;

use VCR\VCRBundle\VCR\Logger;

class PlaybackListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            VCREvents::VCR_AFTER_PLAYBACK => 'onPlayback',
        );
    }

    public function onPlayback(AfterPlaybackEvent $event): void
    {
        $this->logger->logPlayback(
            $event->getRequest(),
            $event->getResponse(),
            $event->getCassette()
        );
    }
}
