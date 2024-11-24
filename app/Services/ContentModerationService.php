<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentModerationService
{
    private string $apiToken;
    private string $accountId;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiToken = config('services.cloudflare.ai_token');
        $this->accountId = config('services.cloudflare.account_id');
        $this->baseUrl = "https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/ai/run";
    }

    public function moderateContent(string $content): ?string
    {
        try {
            $prompt = <<<EOT
                {$content}
                EOT;

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiToken}",
            ])->post($this->baseUrl . '/@cf/meta/llama-3.2-3b-instruct', [
                'messages' => [
                    ['role' => 'system', 'content' => <<<EOT
                    You are a content moderation system. In the following interactions, you will receive a user-written text related to a proposal, comment, suggestion, complaint, or survey.

                    Your role is to convert the provided message into a concise, neutral, optimistic, and friendly tone. Ensure the message maintains the original intent while fostering a positive and collaborative atmosphere.

                    Keep in mind that your purpose is to help build a happy community where neighbors can express concerns and share ideas to improve living together. Typically, the recipient of the message would be facility management.

                    Example input: "Los ascensores llevan malos como 18 meses ya."
                    Expected output: Convert this into a neutral message that highlights the elevators have been malfunctioning for the last 18 months and explains how this impacts the quality of life of the residents.

                    When responding, only provide the converted message in Spanish using JSON format, with a single attribute "message". Do not repeat these instructions in your response.
                    EOT],
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json('result.response');
                return is_string($result) ? $result : null;
            }

            Log::error('Content moderation failed', [
                'error' => $response->json(),
                'content' => $content
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Content moderation error', [
                'error' => $e->getMessage(),
                'content' => $content
            ]);

            return null;
        }
    }

    public function preflight(string $content): ?string
    {
        try {
            $prompt = <<<EOT
                {$content}
                EOT;
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiToken}",
            ])->post($this->baseUrl . '/@cf/meta/llama-3.2-11b-vision-instruct', [
                'messages' => [
                    ['role' => 'system', 'content' => <<<EOT
                        Eres un sistema automatizado de prevalidación de contenido experto que evalúa si un mensaje es suficientemente completo y relevante para ser publicado. Tu tarea es analizar el contenido proporcionado y determinar si cumple con los estándares de suficiencia.

                        - **Si el contenido es insuficiente:** Devuelve un objeto JSON con `"ready": 0` y un `"message"` que contenga una sugerencia constructiva, amigable, positiva, empática y concisa para mejorar el mensaje.
                        - **Si el contenido es suficiente:** Devuelve un objeto JSON con `"ready": 1` y un `"message"` indicando que el mensaje está listo para ser publicado.

                        **Instrucciones:**
                        1. **Evaluación del Contenido:** Analiza el mensaje recibido para determinar si es suficientemente informativo, claro y relevante.
                        2. **Respuesta Apropiada:**
                        - **Insuficiente:** Proporciona una retroalimentación específica y constructiva sin añadir información adicional que no esté presente en el mensaje original.
                        - **Suficiente:** Indica que el mensaje está listo para ser publicado de manera clara y concisa.

                        **Requisitos:**
                        - La respuesta debe ser un objeto JSON válido.
                        - Utiliza exclusivamente el idioma español.
                        - No añadas información, personajes, o detalles que no estén presentes en el mensaje original.
                        - Mantén un tono amigable, positivo y empático.
                        - La respuesta debe ser clara, respetuosa y concisa.
                        - **No combines retroalimentación con aprobación** en una sola respuesta.
                        - No incluyas explicaciones adicionales fuera del objeto JSON.
                        - Mantén la respuesta breve y al punto.
                    EOT],
                    ['role' => 'system', 'content' => 'No respondas en formato markdown'],
                    ['role' => 'system', 'content' => 'Sugerencia constructiva, amigable y concisa para mejorar el mensaje'],
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json('result.response');
                if (is_string($result)) {
                    $cleanResult = preg_replace('/```json\s*|\s*```/', '', $result);
                    $decodedResult = json_decode($cleanResult, true);
                    if (isset($decodedResult['message']) && isset($decodedResult['ready'])) {
                        return json_encode($decodedResult);
                    }
                }
            }

            Log::error('Content moderation failed', [
                'error' => $response->json(),
                'content' => $content
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Content moderation error', [
                'error' => $e->getMessage(),
                'content' => $content
            ]);

            return null;
        }
    }
}
