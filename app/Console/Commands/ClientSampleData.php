<?php

namespace App\Console\Commands;

use App\Comment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Sample data for the Clients section: travel-agency clients with contact
 * people and a note. Names start with "QA Sample"; the ids are kept in
 * storage/app/sample-data/clients.json so `--remove` deletes exactly those.
 */
class ClientSampleData extends Command
{
    protected $signature = 'clients:sample-data {--remove : Delete the sample records instead of creating them}';

    protected $description = 'Create (or remove) QA Sample clients with contacts';

    const PREFIX = 'QA Sample ';

    // [name, country, city, address, account no, phone, email domain, contacts[[name, mobile, phone, email user]]]
    const CLIENTS = [
        ['Danube Tours GmbH', 'AT', 'Vienna', 'Mariahilfer Strasse 45, 1060 Vienna', 'AT61 1904 3002 3457 3201', '+43 1 586 0000', 'danube-tours.example.com',
            [['Katharina Huber', '+43 664 120 3344', '+43 1 586 0011', 'k.huber'], ['Florian Wagner', '+43 664 555 7788', '+43 1 586 0012', 'f.wagner']]],
        ['Bohemia Travel s.r.o.', 'CZ', 'Prague', 'Vodickova 30, 110 00 Prague 1', 'CZ65 0800 0000 1920 0014 5399', '+420 224 000 111', 'bohemia-travel.example.com',
            [['Petra Svobodova', '+420 777 200 300', '+420 224 000 112', 'p.svobodova']]],
        ['Alpine Journeys AG', 'CH', 'Zürich', 'Bahnhofstrasse 70, 8001 Zürich', 'CH93 0076 2011 6238 5295 7', '+41 44 200 0000', 'alpine-journeys.example.com',
            [['Lukas Meier', '+41 79 300 4455', '+41 44 200 0011', 'l.meier'], ['Sophie Keller', '+41 79 600 7788', '+41 44 200 0012', 's.keller']]],
        ['Pannonia Incoming Kft.', 'HU', 'Budapest', 'Andrassy ut 12, 1061 Budapest', 'HU42 1177 3016 1111 1018 0000 0000', '+36 1 300 1000', 'pannonia-incoming.example.com',
            [['Eszter Kovacs', '+36 30 123 4567', '+36 1 300 1011', 'e.kovacs']]],
    ];

    public function handle()
    {
        return $this->option('remove') ? $this->remove() : $this->create();
    }

    private function manifestPath()
    {
        return storage_path('app/sample-data/clients.json');
    }

    private function create()
    {
        if (is_file($this->manifestPath())) {
            $this->error('Client sample data already exists. Run with --remove first.');
            return 1;
        }

        $now = Carbon::now();
        $authorId = DB::table('users')->orderBy('id')->value('id');

        $ids = DB::transaction(function () use ($now, $authorId) {
            $ids = ['clients' => [], 'client_contacts' => [], 'comments' => []];
            foreach (self::CLIENTS as [$name, $country, $city, $address, $account, $phone, $domain, $contacts]) {
                $clientId = DB::table('clients')->insertGetId([
                    'name' => self::PREFIX . $name,
                    'country' => $country,
                    'city' => DB::table('cities')->where('country', $country)->where('name', $city)->value('id'),
                    'address' => $address,
                    'account_no' => $account,
                    'company_address' => $address,
                    'invoice_address' => 'Accounts Payable, ' . $address,
                    'work_phone' => $phone,
                    'contact_phone' => $contacts[0][1],
                    'work_email' => 'groups@' . $domain,
                    'contact_email' => $contacts[0][3] . '@' . $domain,
                    'password' => Crypt::encryptString('QaSample2026'),
                    'work_fax' => $phone . '9',
                    'department' => 'Group Travel',
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $ids['clients'][] = $clientId;

                foreach ($contacts as [$contactName, $mobile, $workPhone, $user]) {
                    $ids['client_contacts'][] = DB::table('client_contacts')->insertGetId([
                        'client_id' => $clientId, 'full_name' => $contactName, 'mobile_phone' => $mobile,
                        'work_phone' => $workPhone, 'email' => $user . '@' . $domain, // client_contacts has no timestamps
                    ]);
                }
            }

            if ($authorId) {
                $ids['comments'][] = Comment::query()->create([
                    'content' => '<p>' . e(self::PREFIX . 'note: prefers rooming lists 21 days before arrival; invoices in EUR.') . '</p>',
                    'author_id' => $authorId, 'reference_type' => Comment::$services['client'],
                    'reference_id' => $ids['clients'][0], 'status' => 0,
                ])->id;
            }

            return $ids;
        });

        if (!is_dir(dirname($this->manifestPath()))) {
            mkdir(dirname($this->manifestPath()), 0775, true);
        }
        file_put_contents($this->manifestPath(), json_encode($ids, JSON_PRETTY_PRINT));

        $this->table(['clients', 'contacts', 'notes'], [[count($ids['clients']), count($ids['client_contacts']), count($ids['comments'])]]);
        $this->line('Client portal password for the samples: QaSample2026');
        $this->info('Remove them with: php artisan clients:sample-data --remove');

        return 0;
    }

    private function remove()
    {
        if (!is_file($this->manifestPath())) {
            $this->error('No client sample data found (' . $this->manifestPath() . ').');
            return 1;
        }

        $ids = json_decode(file_get_contents($this->manifestPath()), true) ?: [];
        $counts = DB::transaction(function () use ($ids) {
            return [
                'comments' => DB::table('comments')->whereIn('id', $ids['comments'] ?? [])->delete(),
                'client_contacts' => DB::table('client_contacts')->whereIn('id', $ids['client_contacts'] ?? [])->delete(),
                'clients' => DB::table('clients')->whereIn('id', $ids['clients'] ?? [])->delete(),
            ];
        });
        unlink($this->manifestPath());

        $this->table(array_keys($counts), [array_values($counts)]);

        return 0;
    }
}
