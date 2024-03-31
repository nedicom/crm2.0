<?php

namespace App\Http\Controllers;

use App\Models\ClientsModel;
use App\Services\GenerateDocuments\CertCompletion\CertCompletionService;
use Illuminate\Support\Facades\Log;

/** Класс генерации .doc файлов */
class GenerateDocumentController extends Controller
{
    public function __construct(
        private CertCompletionService $service
    ) {}

    /**
     * Генерация документа "Акт выполненных работ"
     * @param ClientsModel $client
     */
    public function certificateCompletion(ClientsModel $client)
    {
        try {
            $templateFile = storage_path("app/public/dogovor/template_certificate_completion.docx");
            // Генерация документа
            $generateFile = ($this->service)($templateFile, $client);

            return response()->download($generateFile,"client_{$client->id}_certificate_completion.docx", [
                'Content-Type' => 'application/docx',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
