<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Event;

class RecommenderService
{
    private string $apiUrl = 'http://127.0.0.1:5000';

    public function getRecommendations(int $userId, int $n = 3): array
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->apiUrl}/recommend/{$userId}", ['n' => $n]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['recommendations'] ?? [];
            }

            return $this->getFallbackRecommendations($userId);

        } catch (\Exception $e) {
            Log::error('Recommender API error: ' . $e->getMessage());
            return $this->getFallbackRecommendations($userId);
        }
    }

    private function getFallbackRecommendations(int $userId): array
    {
        return Event::where('status', 'published')
            ->where('date', '>', now())
            ->whereNotIn('id', function($query) use ($userId) {
                $query->select('event_id')
                    ->from('registrations')
                    ->where('user_id', $userId)
                    ->where('status', 'confirmed');
            })
            ->withCount(['registrations' => function($q) {
                $q->where('status', 'confirmed');
            }])
            ->orderBy('registrations_count', 'desc')
            ->take(3)
            ->get()
            ->map(fn($e) => [
                'id'       => $e->id,
                'title'    => $e->title,
                'category' => $e->category,
                'date'     => $e->date,
                'location' => $e->location,
                'price'    => $e->price,
                'reason'   => 'popularity',
                'score'    => 0,
            ])
            ->toArray();
    }
}
