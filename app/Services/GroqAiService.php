<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqAiService
{
    private const ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';

    /**
     * Convierte la descripción cruda de una falla (a veces dictada por voz) en
     * puntos clave, profesionales y sin relleno. Devuelve null si la IA no
     * está configurada o si la llamada falla, para nunca bloquear el flujo.
     */
    public function resumirFalla(string $descripcion): ?string
    {
        $descripcion = trim($descripcion);

        if ($descripcion === '' || ! config('services.groq.key')) {
            return null;
        }

        $respuesta = $this->preguntar(
            sistema: 'Eres un asistente que ayuda a mecánicos de flotillas de autobuses a documentar fallas reportadas por operadores. '
                .'Recibes la descripción cruda de una falla (a veces transcrita por voz, con muletillas o repeticiones) y la conviertes '
                .'en una lista breve de puntos clave, en español, con lenguaje profesional y técnico cuando aplique. '
                .'Elimina saludos, muletillas y cualquier cosa irrelevante al problema mecánico. No inventes información que no esté en el texto original. '
                .'Responde ÚNICAMENTE con la lista de puntos, uno por línea, cada uno iniciando con "- ". No agregues título ni comentarios.',
            usuario: $descripcion,
        );

        return $respuesta ? trim($respuesta) : null;
    }

    /**
     * Determina si un destino de viaje (contrato, viaje, etc.) está en México o
     * en Estados Unidos, para clasificar los movimientos de Egresos e Ingresos
     * por país. Ante cualquier duda o fallo de la IA, cae a "mexico" por ser la
     * operación base de la empresa.
     */
    public function determinarPais(?string $destino): string
    {
        $destino = trim((string) $destino);

        if ($destino === '' || ! config('services.groq.key')) {
            return 'mexico';
        }

        $respuesta = $this->preguntar(
            sistema: 'Eres un clasificador geográfico para una empresa de transporte en autobús con base en San Luis Potosí, México. '
                .'Se te da un texto libre con un destino o ruta de viaje (puede incluir ciudades, estados, abreviaturas, o texto ambiguo). '
                .'Determina si ese destino está en México o en Estados Unidos. Si el texto menciona más de un lugar, usa el destino final del viaje. '
                .'Si no puedes determinarlo con confianza, responde "mexico" por defecto. '
                .'Responde ÚNICAMENTE con una palabra en minúsculas: "mexico" o "usa". Sin puntuación, sin explicación.',
            usuario: $destino,
            maxTokens: 200,
        );

        $respuesta = strtolower(trim((string) $respuesta));

        return str_contains($respuesta, 'usa') ? 'usa' : 'mexico';
    }

    private function preguntar(string $sistema, string $usuario, int $maxTokens = 600): ?string
    {
        try {
            $response = Http::withToken(config('services.groq.key'))
                ->timeout(15)
                ->post(self::ENDPOINT, [
                    'model' => config('services.groq.model', 'openai/gpt-oss-120b'),
                    'temperature' => 0.2,
                    'max_tokens' => $maxTokens,
                    'reasoning_effort' => 'low',
                    'messages' => [
                        ['role' => 'system', 'content' => $sistema],
                        ['role' => 'user', 'content' => $usuario],
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('Groq AI request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            return $response->json('choices.0.message.content');
        } catch (\Throwable $e) {
            Log::warning('Groq AI request threw an exception', ['message' => $e->getMessage()]);

            return null;
        }
    }
}
