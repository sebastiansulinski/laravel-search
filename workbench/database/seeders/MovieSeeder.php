<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::connection()->disableQueryLog();

        Movie::factory(200000)->make()->chunk(1000)->each(function (Collection $books) {
            DB::table('movies')->insert($books->toArray());
        });
    }
}
