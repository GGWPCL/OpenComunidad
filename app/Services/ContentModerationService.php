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
}
