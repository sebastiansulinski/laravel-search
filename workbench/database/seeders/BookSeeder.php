<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::connection()->disableQueryLog();

        Book::factory(200000)->make()->chunk(1000)->each(function (Collection $books) {
            DB::table('books')->insert($books->toArray());
        });
    }
}
