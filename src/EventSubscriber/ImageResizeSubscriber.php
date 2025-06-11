<?php

namespace App\EventSubscriber;

use Intervention\Image\ImageManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

class ImageResizeSubscriber implements EventSubscriberInterface
{
    private ImageManager $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::PRE_UPLOAD => 'onPreUpload',
        ];
    }

    public function onPreUpload(Event $event): void
    {
        $object = $event->getObject();

        // Adjust this condition for your actual entity class
        if (!method_exists($object, 'getImageFile')) {
            return;
        }

        $file = $object->getImageFile();
        if ($file === null) {
            return;
        }

        $image = $this->imageManager->read($file->getPathname());

        $image->resize(1000, 1000, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $image->save($file->getPathname(), 80); // Save at 80% quality
    }
}