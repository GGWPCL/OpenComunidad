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
            ])->post($this->baseUrl . '/@cf/meta/llama-3.2-3b-instruct', [
                'messages' => [
                    ['role' => 'system', 'content' => <<<EOT
                    You are a content moderation system. In the following interactions, you will receive a user-written text related to a proposal, comment, suggestion, complaint, or survey.

                    Your role is to convert the provided message into a concise, neutral, optimistic, and friendly tone. Ensure the message maintains the original intent while fostering a positive and collaborative atmosphere.

                    Keep in mind that your purpose is to help build a happy community where neighbors can express concerns and share ideas to improve living together. Typically, the recipient of the message would be facility management.

                    Example input: "Los ascensores llevan malos como 18 meses ya."
                    Expected output: Analyze the input, and verify that it has enough information to consider it a valid candidate for a proposal, suggestion or poll.

                    When responding, only provide a "ready" boolean attribute JSON format indicating whether the message is enough to be considered a valid post, and a "message" attribute with some concise suggestions on what could be missing if the input is not ready. message MUST be in spanish. Keep message empty if the input meets the minimum criteria
                    EOT],
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json('result.response');
                if (is_string($result)) {
                    $cleanResult = preg_replace('/```json\s*|\s*```/', '', trim($result));

                    try {
                        $decodedResult = json_decode($cleanResult, true, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $e) {
                        try {
                            $decodedResult = json_decode(stripslashes($cleanResult), true, 512, JSON_THROW_ON_ERROR);
                        } catch (\JsonException $e2) {
                            Log::error('JSON parsing failed after both attempts', [
                                'error' => $e2->getMessage(),
                                'result' => $result,
                                'cleaned' => $cleanResult
                            ]);
                            return null;
                        }
                    }

                    if (isset($decodedResult['message']) && isset($decodedResult['ready'])) {
                        if (is_string($decodedResult['ready'])) {
                            $decodedResult['ready'] = filter_var($decodedResult['ready'], FILTER_VALIDATE_BOOLEAN);
                        }
                        return json_encode($decodedResult, JSON_THROW_ON_ERROR);
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
