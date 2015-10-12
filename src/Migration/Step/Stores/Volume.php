<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Migration\Step\Stores;

use Migration\App\Step\AbstractVolume;
use Migration\Resource;
use Migration\App\ProgressBar;
use Migration\Logger\Logger;

/**
 * Class Volume
 */
class Volume extends AbstractVolume
{
    /**
     * @var Resource\Source
     */
    protected $source;

    /**
     * @var Resource\Destination
     */
    protected $destination;

    /**
     * @var ProgressBar\LogLevelProcessor
     */
    protected $progress;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param ProgressBar\LogLevelProcessor $progress
     * @param Resource\Source $source
     * @param Resource\Destination $destination
     * @param Helper $helper
     * @param Logger $logger
     */
    public function __construct(
        ProgressBar\LogLevelProcessor $progress,
        Resource\Source $source,
        Resource\Destination $destination,
        Helper $helper,
        Logger $logger
    ) {
        $this->progress = $progress;
        $this->source = $source;
        $this->destination = $destination;
        $this->helper = $helper;
        parent::__construct($logger);
    }

    /**
     * {@inheritdoc}
     */
    public function perform()
    {
        $this->progress->start(count($this->helper->getDocumentList()));
        foreach ($this->helper->getDocumentList() as $sourceName => $destinationName) {
            $this->progress->advance();
            if ($this->source->getRecordsCount($sourceName) != $this->destination->getRecordsCount($destinationName)) {
                $this->errors[] = 'Mismatch of entities in the document: ' . $destinationName;
            }
        }
        $this->progress->finish();
        return $this->checkForErrors(Logger::ERROR);
    }
}
