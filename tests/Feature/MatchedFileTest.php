<?php

namespace Tests\Feature;

use App\Client;
use App\Pattern;
use Carbon\Carbon;
use Tests\TestCase;
use App\MatchedFile;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class MatchedFileTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_matched_file_can_be_entered_for_a_valid_client()
    {
        // given a client
        $client = factory(Client::class)->create();

        // act
        $this->post("/api/v1/clients/{$client->name}/matches", [
            'file' => 'some_file',
            'file_created_at' => '2017-01-01 12:00:00',
            'file_modified_at' => '2017-01-01 12:00:00',
            'pattern_id' => factory(Pattern::class)->create()->id
        ])

        // database assertions
        ->assertDatabaseHas('matched_files', [
            'client_id' => $client->id,
            'file_created_at' => '2017-01-01 12:00:00',
            'file_modified_at' => '2017-01-01 12:00:00',
            'file' => 'some_file'
        ])

        // response assertions
        ->response()
            ->assertStatus(201);
    }

    /** @test */
    function a_matched_file_can_be_entered_for_a_client_with_a_pattern_that_will_be_looked_up()
    {
        // given a client
        $client = factory(Client::class)->create();
        $pattern = factory(Pattern::class)->create(['name' => 'foo']);
        $now = Carbon::now()->format('Y-m-d H:i:s');

        // act
        $this->post("/api/v1/clients/{$client->name}/matches", [
            'file' => 'some_file',
            'pattern' => $pattern->name,
            'file_modified_at' => $now,
            'file_created_at' => $now,
        ])

        // database assertions
        ->assertDatabaseHas('matched_files', [
            'client_id' => $client->id,
            'pattern_id' => $pattern->id,
            'file_modified_at' => $now,
            'file_created_at' => $now,
            'file' => 'some_file'
        ])

        // response assertions
        ->response()
            ->assertStatus(201);
    }

    /** @test */
    function a_matched_file_cannot_be_entered_for_an_invalid_client()
    {
        // act
        $this->post("/api/v1/clients/invalid_client_name/matches", [
            'file' => 'some_file',
            'pattern_id' => factory(Pattern::class)->create()->id
        ])

        // database assertions
        ->assertDatabaseMissing('matched_files', [
            'file' => 'some_file'
        ])

        // response assertions
        ->response()
            ->assertStatus(404);
    }

    /** @test */
    function a_matched_file_cannot_be_entered_for_an_invalid_pattern()
    {
        // given a valid client
        $client = factory(Client::class)->create();

        // act
        $this->from("/api/v1/clients/{$client->name}/matches")
            ->post("/api/v1/clients/{$client->name}/matches", [
                'file' => 'some_file',
                'pattern_id' => 9999 // some invalid pattern id
            ])

        // database assertions
        ->assertDatabaseMissing('matched_files', [
            'file' => 'some_file'
        ])

        // response assertions
        ->response()
            ->assertStatus(422);
    }

    /** @test */
    function a_matched_file_is_incremented_when_it_is_matched_again_by_the_same_client()
    {
        // given a matched file
        $file = factory(MatchedFile::class)->create();
        $now = Carbon::now()->format('Y-m-d H:i:s');

        // act
        $this->post("/api/v1/clients/{$file->client->name}/matches", [
            'file' => $file->file,
            'pattern_id' => $file->pattern_id,
            'file_created_at' => $now,
            'file_modified_at' => $now
        ])

        ->response()
            ->assertStatus(202);

        $this->assertEquals(2, $file->fresh()->times_matched);
    }

    /** @test */
    function a_matched_file_can_be_acknowledged_by_an_admin()
    {
        // given a matched file
        $file = factory(MatchedFile::class)->create();

        // act
        $this
        ->actingAsAdmin()
        ->post("/api/v1/clients/{$file->client->name}/matches/{$file->id}/acknowledge")

        // assert
        ->response()
            ->assertStatus(202);

        $this->assertTrue( !! $file->fresh()->acknowledged_flag );
    }

    /** @test */
    function matched_file_can_be_simultaneously_acknowledged_by_an_admin()
    {
        $this->disableExceptionHandling();

        // given a matched file
        $files = factory(MatchedFile::class,5)->create();

        // act
        $this
        ->actingAsAdmin()
        ->post("/api/v1/matches/acknowledge")

        // assert
        ->response()
            ->assertStatus(202);

        $files->each( function($file) {
            $this->assertTrue( !! $file->fresh()->acknowledged_flag );
        });
    }

    /** @test */
    function a_matched_file_can_be_muted_by_an_admin()
    {
        // given a matched file
        $file = factory(MatchedFile::class)->create();

        // act
        $this
        ->actingAsAdmin()
        ->post("/api/v1/clients/{$file->client->name}/matches/{$file->id}/mute")

        // assert
        ->response()
            ->assertStatus(202);

        $this->assertTrue( !! $file->fresh()->muted_flag );
    }

    /** @test */
    function multiple_matched_files_can_be_muted_by_an_admin()
    {
        $this->disableExceptionHandling();

        $file1 = factory(MatchedFile::class)->create();
        $file2 = factory(MatchedFile::class)->create();

        $this->actingAsAdmin()
            ->post("/api/v1/matches/_mute", [
                "matches" => [$file1->id, $file2->id]
            ])

            ->response()
                ->assertStatus(202);

        $this->assertTrue( !! $file1->fresh()->muted_flag );
        $this->assertTrue( !! $file2->fresh()->muted_flag );
    }

    /** @test */
    function multiple_matched_files_can_be_unmuted_by_an_admin()
    {
        $this->disableExceptionHandling();

        $file1 = factory(MatchedFile::class)->states('muted')->create();
        $file2 = factory(MatchedFile::class)->states('muted')->create();

        $this->actingAsAdmin()
            ->post("/api/v1/matches/_unmute", [
                "matches" => [$file1->id, $file2->id]
            ])

            ->response()
                ->assertStatus(202);

        $this->assertFalse( !! $file1->fresh()->muted_flag );
        $this->assertFalse( !! $file2->fresh()->muted_flag );
    }

    /** @test */
    function a_muted_matched_file_can_be_unmuted_by_an_admin()
    {
        // given a matched file
        $file = factory(MatchedFile::class)->states('muted')->create();

        // act
        $this
        ->actingAsAdmin()
        ->post("/api/v1/clients/{$file->client->name}/matches/{$file->id}/unmute")

        // assert
        ->response()
            ->assertStatus(202);

        $this->assertFalse( !! $file->fresh()->muted_flag );
    }

    /** @test */
    function a_valid_file_with_an_invalid_client_cannot_be_muted_or_unmuted()
    {
        // given a matched file
        $file = factory(MatchedFile::class)->states('muted')->create();

        // act
        $this
        ->actingAsAdmin()
        ->post("/api/v1/clients/invalid-client-name/matches/{$file->id}/unmute")

        // assert
        ->response()
            ->assertStatus(404);

        $this->assertTrue( !! $file->fresh()->muted_flag );
    }

    /** @test */
    function a_valid_file_with_the_wrong_client_cannot_be_muted_or_unmuted()
    {
        // given a matched file
        $file = factory(MatchedFile::class)->states('muted')->create();

        // and a different client
        $other_client = factory(Client::class)->create();

        // act
        $this
        ->actingAsAdmin()
        ->post("/api/v1/clients/{$other_client->name}/matches/{$file->id}/unmute")

        // assert
        ->response()
            ->assertStatus(404);

        $this->assertTrue( !! $file->fresh()->muted_flag );
    }

    /** @test */
    function multiple_matched_files_can_be_exempted_by_filename()
    {
        $this->disableExceptionHandling();

        $file1 = factory(MatchedFile::class)->create();
        $file2 = factory(MatchedFile::class)->create();

        $this
            ->actingAsAdmin()
            ->post("api/v1/matches/_exempt/filename", [
                'matches' => [$file1->id, $file2->id]
            ])

        ->response()
            ->assertStatus(202);

        $this->assertDatabaseHas('exemptions', ['pattern' => $file1->filename]);
        $this->assertDatabaseHas('exemptions', ['pattern' => $file2->filename]);

        $this->assertTrue( $file1->fresh()->muted_flag );
        $this->assertTrue( $file2->fresh()->muted_flag );
    }

    /** @test */
    function multiple_matched_files_can_be_exempted_by_filepath()
    {
        $this->disableExceptionHandling();

        $file1 = factory(MatchedFile::class)->create();
        $file2 = factory(MatchedFile::class)->create();

        $this
            ->actingAsAdmin()
            ->post("api/v1/matches/_exempt", [
                'matches' => [$file1->id, $file2->id]
            ])

        ->response()
            ->assertStatus(202);

        $this->assertDatabaseHas('exemptions', ['pattern' => $file1->file]);
        $this->assertDatabaseHas('exemptions', ['pattern' => $file2->file]);

        $this->assertTrue( $file1->fresh()->muted_flag );
        $this->assertTrue( $file2->fresh()->muted_flag );
    }

    /** @test */
    function a_list_of_matched_files_can_be_fetched()
    {
        factory(MatchedFile::class,5)->create();

        $this
            ->get("/api/v1/matches")
            ->response()
                ->assertStatus(200);

        $this->assertJsonCount(5);
    }

    /** @test */
    function a_single_matched_file_can_be_fetched()
    {
        $file = factory(MatchedFile::class)->create();

        $this
            ->get("/api/v1/matches/{$file->id}")
            ->response()
                ->assertStatus(200)
                ->assertJson(['file' => $file->file]);
    }


}
