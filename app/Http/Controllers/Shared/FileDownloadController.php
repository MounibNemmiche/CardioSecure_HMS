<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecordFile;
use App\Services\AuditService;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    public function __invoke(MedicalRecordFile $file)
    {
        $this->authorize('download', $file);

        AuditService::log('file_downloaded', MedicalRecordFile::class, $file->id);

        return Storage::download($file->file_path, $file->file_name);
    }
}
