<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\BookIndex;

class ImportXmlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $bookId;
    protected $xmlContent;

    /**
     * Create a new job instance.
     */
    public function __construct(int $bookId, $xmlContent)
    {
        $this->bookId = $bookId;
        $this->xmlContent = $xmlContent;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $items = simplexml_load_string($this->xmlContent);

        $this->importItems($this->bookId, $items->item);
    }

    private function importItems(int $bookId, $items, int $bookIndexId = null)
    {
        foreach ($items as $item) {
            $indexTitle = (string) $item['title'];
            $indexPage = (int) $item['page'];

            $newBookIndex = BookIndex::create([
                'book_id' => $bookId,
                'book_index_id' => $bookIndexId,
                'title' => $indexTitle,
                'page' => $indexPage
            ]);

            if (isset($item->children()->item)) {
                $this->importItems($bookId, $item->children()->item, $newBookIndex->id);
            }
        }
    }

}
