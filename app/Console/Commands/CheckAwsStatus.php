<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\AwsStatusLog;
use Carbon\Carbon;

class CheckAwsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-aws-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek Status AWS dan simpan jika mati/hidup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stations = [
            '5000000031' => 'AWS Digi Banyuwangi',
            '3000000007' => 'AWS Maritim Ketapang',
            '3000000046' => 'AWS Maritim Gilimanuk',
        ];

foreach ($stations as $id => $name) {
    $response = Http::get("http://202.90.199.132/aws-new/data/station/latest/$id");

    if (!$response->successful()) {
        $this->error("Gagal mengambil API untuk $name");
        continue;
    }

    $json = $response->json();

    $mati = (
        ($json['rain'] ?? 0) == 0 &&
        ($json['windspeed'] ?? 0) == 0 &&
        ($json['winddir'] ?? 0) == 0 &&
        ($json['rh'] ?? 0) == 0 &&
        ($json['temp'] ?? 0) == 0
    );

    $statusSekarang = $mati ? 'mati' : 'hidup';

    // Ambil status terakhir dari DB
    $last = AwsStatusLog::where('aws_id', $id)
        ->orderBy('id', 'desc')
        ->first();

    if ($statusSekarang === 'mati') {
        // Simpan mati pertama atau jika berubah dari hidup → mati
        AwsStatusLog::create([
            'aws_id' => $id,
            'name' => $name,
            'status' => 'mati',
            'waktu' => Carbon::now('Asia/Jakarta'),
        ]);
        $this->info("AWS $name mati");
    }

    if ($statusSekarang === 'hidup') {
        // Simpan hidup hanya jika sebelumnya sudah pernah mati
        $pernahMati = AwsStatusLog::where('aws_id', $id)
            ->where('status', 'mati')
            ->exists();

        if ($pernahMati && (!$last || $last->status !== 'hidup')) {
            AwsStatusLog::create([
                'aws_id' => $id,
                'name' => $name,
                'status' => 'hidup',
                'waktu' => Carbon::now('Asia/Jakarta'),
            ]);
            $this->info("AWS $name hidup kembali");
        }
    }
}

        return 0;
    }
}
