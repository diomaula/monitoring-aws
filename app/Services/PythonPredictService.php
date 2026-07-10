<?php

namespace App\Services;

class PythonPredictService
{
    public static function predict($data)
    {
        // path python
        $pythonPath = "python";

        // path predict.py
        $scriptPath = escapeshellarg(
            base_path('python_anomali/predict.py')
        );

        // simpan json sementara
        $tempFile = storage_path('app/temp_predict.json');

        file_put_contents(
            $tempFile,
            json_encode($data)
        );

        // command
        $command = "$pythonPath $scriptPath < " . escapeshellarg($tempFile) . " 2>&1";

        // execute
        $output = shell_exec($command);

        // hapus file sementara
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        return [
            'command' => $command,
            'raw_output' => $output,
            'decoded' => json_decode($output, true),
        ];
    }
}