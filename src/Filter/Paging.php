<?php

namespace SebastianSulinski\Search\Filter;

use Illuminate\Support\Number;

class Paging
{
    public string $totalRecordsForHumans;

    public string $matchingRecordsForHumans;

    public int $numberOfPages;

    public string $numberOfPagesForHumans;

    public bool $show;

    /**
     * Paging constructor.
     */
    public function __construct(
        public int $totalRecords,
        public int $matchingRecords,
        public int $currentPage,
        public int $perPage
    ) {
        $this->totalRecordsForHumans = Number::format($totalRecords);
        $this->matchingRecordsForHumans = Number::format($matchingRecords);
        $this->numberOfPages = ceil($this->matchingRecords / $this->perPage);
        $this->numberOfPagesForHumans = Number::format($this->numberOfPages);
        $this->show = $this->numberOfPages > 1;
    }
}
