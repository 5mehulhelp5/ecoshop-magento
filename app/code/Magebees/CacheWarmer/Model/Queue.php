<?php
namespace Magebees\CacheWarmer\Model;

use Magebees\CacheWarmer\Api\QueuePageRepositoryInterface;
use Magebees\CacheWarmer\Exception\LockException;
use Magebees\CacheWarmer\Model\Config\Source\QuerySource;
use Magebees\CacheWarmer\Model\Crawler\Crawler;
use Magebees\CacheWarmer\Model\Queue\ProcessMetaInfo;
use Magebees\CacheWarmer\Model\ResourceModel\Activity;
use Magebees\CacheWarmer\Model\ResourceModel\Queue\Page\Collection as PageCollection;
use Magebees\CacheWarmer\Model\ResourceModel\Queue\Page\CollectionFactory as PageCollectionFactory;
use Magebees\CacheWarmer\Model\Source\PagesProvider;

class Queue
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var PageCollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * @var QueuePageRepository
     */
    private $pageRepository;

    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * @var PagesProvider
     */
    private $pagesProvider;

    /**
     * @var ProcessMetaInfo
     */
    private $processMetaInfo;
	
	protected $batch;

    public function __construct(
        Config $config,
        PageCollectionFactory $pageCollectionFactory,
        QueuePageRepositoryInterface $pageRepository,
        Crawler $crawler,
        PagesProvider $pagesProvider,
        ProcessMetaInfo $processMetaInfo
    ) {
        $this->config = $config;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->pageRepository = $pageRepository;
        $this->crawler = $crawler;
        $this->pagesProvider = $pagesProvider;
        $this->processMetaInfo = $processMetaInfo;
		$this->batch = false;
    }

    protected function lock()
    {
        if ($this->processMetaInfo->isQueueLocked()) {
            throw new LockException(__('Another lock detected (the Warmer queue is in a progress).'));
        }

        $this->processMetaInfo->setIsQueueLocked(true);
    }

    protected function unlock()
    {
        $this->processMetaInfo->setIsQueueLocked(false);
    }

    public function forceUnlock()
    {
        $this->processMetaInfo->setIsQueueLocked(false);
    }
	
	public function setBatchSizeAjax(){
		$this->batch = true;
	}
	
	public function getBatchSizeAjax(){
		return $this->batch;
	}

    public function generate(): array
    {
        $this->lock();
        $processedItems = 0;
        $queueLimit = $this->config->getQueueLimit();
        $sourceType = $this->config->getSourceType();
        $sourcePages = $this->pagesProvider->getSourcePages($sourceType, $queueLimit);

        if (empty($sourcePages)) {
            $this->unlock();

            return [false, $processedItems];
        }

        try {
            $this->pageRepository->clear();
        } catch (\Exception $e) {
            $this->unlock();

            return [false, $processedItems];
        }

        foreach ($sourcePages as $page) {
            $this->pageRepository->addPage($page);
            $processedItems++;

            if (!$this->processMetaInfo->isQueueLocked()) {
                return [false, $processedItems];
            }
        }

        $this->unlock();
        $this->processMetaInfo->setTotalPagesQueued($processedItems);
        $this->processMetaInfo->resetTotalPagesCrawled();

        return [true, $processedItems];
    }

    public function process(): int
    {
        $this->lock();
        $uncachedPagesCollection = $this->getUncachedPages();
        $this->crawler->processPages($uncachedPagesCollection);
        $this->processMetaInfo->addToTotalPagesCrawled($uncachedPagesCollection->count());

        if ((int)$this->config->getSourceType() === QuerySource::SOURCE_ACTIVITY
            && $this->config->isUseVisitParams()
        ) {
            $uncachedPagesCollection = $this->getUncachedActivityPages();
            $this->crawler->processUsingVisitParams($uncachedPagesCollection);
            $this->processMetaInfo->addToTotalPagesCrawled($uncachedPagesCollection->count());
        }

        $this->unlock();

        return $uncachedPagesCollection->count();
    }

    private function getUncachedPages()
    {
        $pageCollection = $this->pageCollectionFactory->create()->setOrder('rate');

        if ((int)$this->config->getSourceType() === QuerySource::SOURCE_ACTIVITY
            && $this->config->isUseVisitParams()
        ) {
            $pageCollection->addFieldToFilter('activity_id', ['null' => true]);
        }

		$batch = $this->getBatchSizeAjax();
		if($batch){
			$pageCollection->setPageSize(5);
		}else{
			$pageCollection->setPageSize($this->config->getBatchSize());
		}



        return $pageCollection;
    }

    private function getUncachedActivityPages(): PageCollection
    {
        $pageCollection = $this->pageCollectionFactory->create()->setOrder('main_table.rate');
        $pageCollection->getSelect()->joinInner(
            ['activity_data' => $pageCollection->getTable(Activity::TABLE_NAME)],
            'main_table.activity_id = activity_data.id',
            [
                'activity_data.mobile',
                'activity_data.currency',
                'activity_data.customer_group'
            ]
        );
        $pageCollection->setPageSize($this->config->getBatchSize());

        return $pageCollection;
    }
}
