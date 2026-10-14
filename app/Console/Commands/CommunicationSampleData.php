<?php

namespace App\Console\Commands;

use App\Comment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Sample data for the Communications menu: email templates (with the ##tags##
 * the template sender fills in) and comment threads on a tour and a hotel.
 * Everything is called "QA Sample ..."; the ids are kept in
 * storage/app/sample-data/communication.json so `--remove` deletes exactly those.
 *
 * Emails themselves are not sampled: the inbox reads the live mail server.
 */
class CommunicationSampleData extends Command
{
    protected $signature = 'communication:sample-data {--remove : Delete the sample records instead of creating them}';

    protected $description = 'Create (or remove) QA Sample email templates and comments';

    const PREFIX = 'QA Sample ';

    // service id (as on the Templates page) => [[name, content], ...]
    const TEMPLATES = [
        0 => [
            ['Hotel Booking Request', '<p>Dear Reservations team,</p><p>On behalf of our group <b>##tour_name##</b> we would like to request the following rooms:</p><p>Arrival: ##arrival##<br>Rooms: ##roomingtypes##<br>Guests: ##pax##</p><p>Please confirm availability and the rate per room. Rooming list: ##roominglist##</p><p>Kind regards</p>'],
            ['Hotel Rooming List Update', '<p>Dear ##name##,</p><p>Please find the updated rooming list for <b>##tour_name##</b> (ref. ##reference##):</p><p>##roominglist##</p><p>Thank you for your help.</p>'],
        ],
        1 => [
            ['Event Ticket Request', '<p>Dear ##name##,</p><p>We would like to book tickets for <b>##pax##</b> guests on ##date## (##time_from## - ##time_to##) for our tour ##tour_name##.</p><p>Please confirm and send the invoice to us.</p>'],
        ],
        2 => [
            ['Guide Booking Request', '<p>Dear ##name##,</p><p>Are you available to guide our group (##pax## guests) on ##date## from ##time_from## to ##time_to##?</p><p>Meeting point: ##address##</p><p>Tour: ##tour_name##</p>'],
        ],
        3 => [
            ['Coach Transfer Request', '<p>Dear ##name##,</p><p>We need a coach for <b>##pax##</b> passengers on ##date##, pick-up at ##time_from## from ##address##.</p><p>Tour: ##tour_name## (ref. ##reference##). Please confirm the vehicle and driver.</p>'],
        ],
        4 => [
            ['Restaurant Group Reservation', '<p>Dear ##name##,</p><p>We would like to reserve a table for <b>##pax##</b> guests on ##date## at ##time_from##.</p><p>Menu: ##menu##</p><p>Price per person: ##price_for_one##</p><p>Tour: ##tour_name##</p>'],
        ],
    ];

    // [reference type, reference id (null = find), text, replies]
    const COMMENTS = [
        ['tour', 131, 'Client asked to move the Vienna city tour to the afternoon. Checking with the guide.', ['Guide confirmed 14:00 start, itinerary updated.']],
        ['tour', 131, 'Rooming list received from the client: 22 double rooms, 4 singles.', []],
        ['hotel', 'QA Test Hotel Prague', 'Hotel confirmed early check-in from 11:00 for our groups at no extra cost.', ['Noted in the hotel agreement.']],
    ];

    public function handle()
    {
        return $this->option('remove') ? $this->remove() : $this->create();
    }

    private function manifestPath()
    {
        return storage_path('app/sample-data/communication.json');
    }

    private function create()
    {
        if (is_file($this->manifestPath())) {
            $this->error('Communication sample data already exists. Run with --remove first.');
            return 1;
        }

        $authorId = DB::table('users')->orderBy('id')->value('id');
        $now = Carbon::now();

        $ids = DB::transaction(function () use ($authorId, $now) {
            $ids = ['templates' => [], 'comments' => []];
            foreach (self::TEMPLATES as $serviceId => $templates) {
                foreach ($templates as [$name, $content]) {
                    $ids['templates'][] = DB::table('templates')->insertGetId([
                        'service_id' => $serviceId, 'name' => self::PREFIX . $name, 'content' => $content,
                        'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
            }

            foreach (self::COMMENTS as [$type, $reference, $text, $replies]) {
                $referenceId = $type === 'hotel' && !is_numeric($reference)
                    ? DB::table('hotels')->where('name', $reference)->value('id')
                    : (DB::table($type === 'tour' ? 'tours' : 'hotels')->where('id', $reference)->exists() ? $reference : null);
                if (!$referenceId) {
                    continue; // that record does not exist on this server
                }
                // through the model, so the content is sanitised like any other comment
                $parent = Comment::query()->create([
                    'content' => '<p>' . e(self::PREFIX . 'note: ' . $text) . '</p>', 'author_id' => $authorId,
                    'reference_type' => Comment::$services[$type], 'reference_id' => $referenceId, 'status' => 0,
                ]);
                $ids['comments'][] = $parent->id;
                foreach ($replies as $reply) {
                    $ids['comments'][] = Comment::query()->create([
                        'content' => '<p>' . e(self::PREFIX . 'reply: ' . $reply) . '</p>', 'author_id' => $authorId,
                        'reference_type' => Comment::$services[$type], 'reference_id' => $referenceId, 'status' => 0,
                        'parent' => $parent->id,
                    ])->id;
                }
            }

            return $ids;
        });

        if (!is_dir(dirname($this->manifestPath()))) {
            mkdir(dirname($this->manifestPath()), 0775, true);
        }
        file_put_contents($this->manifestPath(), json_encode($ids, JSON_PRETTY_PRINT));

        $this->table(['email templates', 'comments (incl. replies)'], [[count($ids['templates']), count($ids['comments'])]]);
        $this->info('Remove them with: php artisan communication:sample-data --remove');

        return 0;
    }

    private function remove()
    {
        if (!is_file($this->manifestPath())) {
            $this->error('No Communication sample data found (' . $this->manifestPath() . ').');
            return 1;
        }

        $ids = json_decode(file_get_contents($this->manifestPath()), true) ?: [];
        $counts = DB::transaction(function () use ($ids) {
            return [
                'templates' => DB::table('templates')->whereIn('id', $ids['templates'] ?? [])->delete(),
                'comments' => DB::table('comments')->whereIn('id', $ids['comments'] ?? [])->delete(),
            ];
        });
        unlink($this->manifestPath());

        $this->table(array_keys($counts), [array_values($counts)]);

        return 0;
    }
}
