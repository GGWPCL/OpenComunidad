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
            ])->post($this->baseUrl . '/@cf/mistral/mistral-7b-instruct-v0.2-lora', [
                'messages' => [
                    ['role' => 'system', 'content' => <<<EOT
                        Eres un sistema de moderación de contenido automatizado experto que recibe mensajes con contenido potencialmente inapropiado y los transforma en versiones adecuadas para todo público.

                        Tu tarea es devolver un mensaje moderado en español que:
                        1. Identifique claramente el problema expresado en el mensaje original.
                        2. Plantee una solución o acción constructiva para resolverlo.
                        3. Destaque el impacto positivo que tendría en la comunidad si se resolviera el problema.

                        El mensaje debe ser claro, respetuoso, conciso y adecuado para todo público. No añadas explicaciones ni contexto adicional fuera del formato descrito.

                        Ejemplo:

                        Entrada:
                        CTM POR LA MIERDA COMO EL ASCENSOR SIGUE MALO, YA 8 MESES SIN FUNCIONARRRRR CHUPENLOOO

                        Salida:
                        El problema con el ascensor, que lleva 8 meses sin funcionar, afecta significativamente la calidad de vida de los vecinos, especialmente de quienes tienen movilidad reducida o necesitan cargar objetos pesados. Sería ideal priorizar su reparación lo antes posible. Resolverlo mejoraría la accesibilidad, facilitaría el día a día de todos y fortalecería el sentido de comunidad.

                        Entrada:
                        ME TIENEN CHATO, A VER SI DEJAN DE HACER RUIDO TODA LA NOCHE, NO SE PUEDE DORMIR

                        Salida:
                        El ruido constante durante las noches dificulta el descanso de los vecinos, lo cual puede afectar su salud y bienestar general. Sería ideal establecer horarios de silencio o llegar a un acuerdo para reducir el ruido. Esto promovería una convivencia más armoniosa y un ambiente más saludable para toda la comunidad.

                        Ahora modera este contenido:
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
