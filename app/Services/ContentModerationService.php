<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;


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

    public function moderateContent(string $content, string $inputType = 'content', string $categoryName = 'proposals'): ?string
    {
        Log::info("moderateContent(" . $content . ", " . $inputType . ', ' . $categoryName);
        try {
            // Dynamic input type description
            $inputTypeMessage = match ($inputType) {
                'title' => 'The content provided is a post title. Keep it short as the post content will later explain everything. If its good enough as a executive email subject, keep it as it is.',
                'content' => 'The content provided is a post content.',
                'poll_option' => 'The content provided is a poll option.',
                'poll_title' => 'The content provided is a poll title. Keep it short, and it can be a question. If its clear enough, dont change it.',
                default => 'The content provided is unspecified.',
            };
    
            // Dynamic category description
            $categoryDescription = match ($categoryName) {
                'proposals' => 'Share your improvement proposals and suggestions with the community.',
                'poll' => 'Create polls to gather the community’s opinion.',
                'imagine' => 'Share creative ideas to improve the community.',
                'requests' => 'Raise a concern or situation that affects the community.',
                default => 'Unspecified category.',
            };
    
            // System prompt with dynamic input type and category descriptions
            $systemPrompt = <<<EOT
            Ensure that user posts are re-worded to maintain a concise, neutral, and constructive tone, while keeping the original meaning intact.
    
            These posts are intended for a platform where residents can suggest improvements, voice complaints, or conduct surveys. Your role is to refine inputs for diplomatic and non-inflammatory language. The platform is not a place where other people can leave comments, so it's not a conversation, but a place to put requests, and others can upvote it.
    
            {$inputTypeMessage}
    
            Category Description: {$categoryDescription}
    
            # Steps
    
            - Identify if the given content requires re-wording in order to make it neutral, constructive, and polite.
            - Re-write any content identified as lacking neutrality or being confrontational to instead convey a diplomatic and constructive message.
            - Absorb the tone of the original message while eliminating any overly emotional or accusatory language.
            - Maintain clarity and conciseness throughout the re-written post.
    
            # Output Format
    
            The output should be a single paragraph of text that preserves the intent of the input but rephrased to meet the criteria for neutrality, politeness, and constructiveness. Don't surround the answer with quotes. Language will tipically be Spanish.
    
            # Examples
    
            **Input:**  
            "Es impresentable que los ascensores lleven malos tanto tiempo."   
            (This message comes across as on the attack, implying blame to the building manager. It should instead be direct but neutral.)
    
            **Output:**  
            "Es importante resolver el problema de los ascensores que llevan fallando tanto tiempo. Agradeceríamos cualquier actualización sobre los avances."   
            (Here, the message remains clear and conveys urgency, but does so in a diplomatic and constructive way.)
            
            EOT;
    
            // Construct the messages array for GPT-4 Mini
            $messages = [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $content,
                ],
            ];
    
            // Use the OpenAI PHP SDK to make the request
            $response = OpenAI::chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
            ]);
    
            // Extract the response message
            $result = data_get($response, 'choices.0.message.content');
            Log::info($result);
            return is_string($result) ? $result : null;
        } catch (\Exception $e) {
            Log::error('Content moderation error', [
                'error' => $e->getMessage(),
                'content' => $content,
            ]);
    
            return null;
        }
    }

 public function preflight(string $content, string $inputType = 'content', string $categoryName = 'proposals'): ?string
{
    try {
        // Dynamic input type description
        $inputTypeMessage = match ($inputType) {
            'title' => 'The content provided is a post title. It should make sense for the chosen category.',
            'content' => 'The content provided is a post content. It should be related to the category that was chosen, and include enough details to be able to later transform this answer to a constructive, neutral and positive communication style.',
            'poll_option' => 'The content provided is a poll option. It can be a single word, or few words.',
            'poll_title' => 'The content provided is a poll title. It should be concise, clear, and may be phrased as a question.',
            default => 'The content provided is unspecified.',
        };

        // Dynamic category description
        $categoryDescription = match ($categoryName) {
            'proposals' => 'Share your improvement proposals and suggestions with the community.',
            'poll' => 'Create polls to gather the community’s opinion.',
            'imagine' => 'Share creative ideas to improve the community.',
            'requests' => 'Raise a concern or situation that affects the community.',
            default => 'Unspecified category.',
        };

        // System prompt with dynamic input type and category descriptions
        $systemPrompt = <<<EOT
        You are a content moderation system. In the following interactions, you will receive a user-written text related to a proposal, comment, suggestion, complaint, or survey.

        Your role is to analyze the provided message and determine if it has enough information to be considered a valid candidate for a proposal, suggestion, or poll.

        {$inputTypeMessage}

        Category Description: {$categoryDescription}

        Respond in the following structured JSON format:
        {
            "ready": <boolean>, // true if the input is sufficient, false otherwise
            "message": <string> // A concise message in Spanish with suggestions for improvement if not ready, or an empty string if ready
        }

        # Example

        **Input:**  
        "Los ascensores llevan malos como 18 meses ya."
        **Output:**  
        {
            "ready": false,
            "message": "Por favor, agrega detalles sobre el impacto o sugerencias específicas para resolver el problema."
        }
        EOT;

        // Construct the messages array for GPT
        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
            [
                'role' => 'user',
                'content' => $content,
            ],
        ];

        // Use the OpenAI PHP SDK to send the request
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => $messages,
        ]);

        // Extract the response message
        $result = data_get($response, 'choices.0.message.content');

        // Decode and validate the JSON structured output
        $decodedResult = json_decode($result, true, 512, JSON_THROW_ON_ERROR);

        if (isset($decodedResult['ready']) && isset($decodedResult['message'])) {
            return json_encode($decodedResult, JSON_THROW_ON_ERROR);
        }

        Log::error('Invalid response structure from OpenAI', [
            'response' => $result,
            'content' => $content,
        ]);

        return null;
    } catch (\JsonException $e) {
        Log::error('JSON parsing error', [
            'error' => $e->getMessage(),
            'content' => $content,
        ]);

        return null;
    } catch (\Exception $e) {
        Log::error('Content moderation error', [
            'error' => $e->getMessage(),
            'content' => $content,
        ]);

        return null;
    }
}
}
