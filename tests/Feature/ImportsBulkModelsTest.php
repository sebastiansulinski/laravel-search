<?php

namespace SebastianSulinski\SearchTests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use SebastianSulinski\SearchTests\BaseTest;

class ImportsBulkModelsTest extends BaseTest
{
    use RefreshDatabase;

    #[Test]
    public function runs_test(): void
    {
        $this->post(route('search'))
            ->assertStatus(200);
    }
}
