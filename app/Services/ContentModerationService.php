<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentModerationService
{
    /**
     * Danh sách từ khóa nhạy cảm (blacklist)
     */
    private function getBlacklistWords(): array
    {
        return config('moderation.blacklist_words', []);
    }

    /**
     * Kiểm tra nội dung text có vi phạm không
     * 
     * @param string $text Nội dung cần kiểm tra
     * @return array ['is_violated' => bool, 'reason' => string, 'violations' => array]
     */
    public function checkText(string $text): array
    {
        $text = mb_strtolower($text, 'UTF-8');
        $violations = [];
        
        // Kiểm tra blacklist
        foreach ($this->getBlacklistWords() as $word) {
            if (mb_strpos($text, mb_strtolower($word, 'UTF-8')) !== false) {
                $violations[] = [
                    'type' => 'blacklist',
                    'word' => $word,
                    'severity' => 'high'
                ];
            }
        }

        // Kiểm tra bằng OpenAI Moderation API (nếu được bật)
        if (config('moderation.use_openai', false)) {
            $aiResult = $this->checkWithOpenAI($text);
            if ($aiResult['is_violated']) {
                $violations = array_merge($violations, $aiResult['violations']);
            }
        }

        // Kiểm tra spam patterns
        $spamPatterns = $this->checkSpamPatterns($text);
        if (!empty($spamPatterns)) {
            $violations = array_merge($violations, $spamPatterns);
        }

        $isViolated = !empty($violations);
        $reason = $isViolated ? $this->generateReason($violations) : '';

        return [
            'is_violated' => $isViolated,
            'reason' => $reason,
            'violations' => $violations,
        ];
    }

    /**
     * Kiểm tra bằng OpenAI Moderation API
     */
    private function checkWithOpenAI(string $text): array
    {
        if (!config('moderation.use_openai', false)) {
            return ['is_violated' => false, 'violations' => []];
        }

        $apiKey = config('services.openai.api_key');
        
        if (!$apiKey) {
            return ['is_violated' => false, 'violations' => []];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(5)->post('https://api.openai.com/v1/moderations', [
                'input' => $text,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $result = $data['results'][0] ?? null;
                
                if ($result && $result['flagged']) {
                    $categories = $result['category_scores'] ?? [];
                    $violations = [];
                    
                    $threshold = config('moderation.openai_threshold', 0.5);
                    foreach ($categories as $category => $score) {
                        if ($score > $threshold) {
                            $violations[] = [
                                'type' => 'openai',
                                'category' => $category,
                                'score' => $score,
                                'severity' => $score > 0.8 ? 'high' : 'medium'
                            ];
                        }
                    }
                    
                    return [
                        'is_violated' => !empty($violations),
                        'violations' => $violations
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('OpenAI Moderation API error: ' . $e->getMessage());
        }

        return ['is_violated' => false, 'violations' => []];
    }

    /**
     * Kiểm tra spam patterns
     */
    private function checkSpamPatterns(string $text): array
    {
        $violations = [];
        
        // Kiểm tra URL spam (nhiều URL trong text ngắn)
        $urlCount = preg_match_all('/https?:\/\/[^\s]+/i', $text);
        if ($urlCount > 2 && mb_strlen($text) < 200) {
            $violations[] = [
                'type' => 'spam',
                'pattern' => 'multiple_urls',
                'severity' => 'medium'
            ];
        }

        // Kiểm tra số điện thoại spam (nhiều số trong text ngắn)
        $phoneCount = preg_match_all('/0\d{9,10}/', $text);
        if ($phoneCount > 2 && mb_strlen($text) < 200) {
            $violations[] = [
                'type' => 'spam',
                'pattern' => 'multiple_phones',
                'severity' => 'medium'
            ];
        }

        // Kiểm tra ký tự lặp lại (spam)
        if (preg_match('/(.)\1{10,}/u', $text)) {
            $violations[] = [
                'type' => 'spam',
                'pattern' => 'repeated_characters',
                'severity' => 'low'
            ];
        }

        // Kiểm tra chữ in hoa quá nhiều (SHOUTING)
        $upperCount = mb_strlen(preg_replace('/[^A-ZÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ]/u', '', $text));
        $totalChars = mb_strlen(preg_replace('/[^a-zA-ZÀ-ỹ]/u', '', $text));
        if ($totalChars > 0 && ($upperCount / $totalChars) > 0.7 && $totalChars > 20) {
            $violations[] = [
                'type' => 'spam',
                'pattern' => 'excessive_caps',
                'severity' => 'low'
            ];
        }

        return $violations;
    }

    /**
     * Tạo lý do từ vi phạm
     */
    private function generateReason(array $violations): string
    {
        $reasons = [];
        
        foreach ($violations as $violation) {
            switch ($violation['type']) {
                case 'blacklist':
                    $reasons[] = 'Chứa từ ngữ không phù hợp';
                    break;
                case 'openai':
                    $category = $violation['category'] ?? '';
                    $reasons[] = $this->translateAICategory($category);
                    break;
                case 'spam':
                    $reasons[] = 'Nội dung có dấu hiệu spam';
                    break;
            }
        }

        return implode(', ', array_unique($reasons));
    }

    /**
     * Dịch category OpenAI sang tiếng Việt
     */
    private function translateAICategory(string $category): string
    {
        $translations = [
            'hate' => 'Nội dung kích động thù địch',
            'hate/threatening' => 'Nội dung đe dọa',
            'harassment' => 'Nội dung quấy rối',
            'harassment/threatening' => 'Nội dung quấy rối đe dọa',
            'self-harm' => 'Nội dung tự hại',
            'self-harm/intent' => 'Có ý định tự hại',
            'self-harm/instructions' => 'Hướng dẫn tự hại',
            'sexual' => 'Nội dung tình dục',
            'sexual/minors' => 'Nội dung tình dục trẻ em',
            'violence' => 'Nội dung bạo lực',
            'violence/graphic' => 'Nội dung bạo lực đồ họa',
        ];

        return $translations[$category] ?? 'Nội dung không phù hợp';
    }

    /**
     * Kiểm tra hình ảnh (sử dụng OpenAI Vision API hoặc service khác)
     * 
     * @param string $imagePath Đường dẫn ảnh
     * @return array
     */
    public function checkImage(string $imagePath): array
    {
        // OpenAI Vision API không có moderation riêng, cần dùng image classification
        // Hoặc dùng service khác như Google Cloud Vision API
        
        // Tạm thời chỉ kiểm tra kích thước và format
        if (!file_exists($imagePath)) {
            return [
                'is_violated' => false,
                'reason' => '',
                'violations' => []
            ];
        }

        // Có thể tích hợp thêm API khác ở đây
        // Ví dụ: Google Cloud Vision API SafeSearch
        
        return [
            'is_violated' => false,
            'reason' => '',
            'violations' => []
        ];
    }

    /**
     * Kiểm tra nội dung listing (title + description)
     */
    public function checkListing(string $title, string $description): array
    {
        $titleCheck = $this->checkText($title);
        $descriptionCheck = $this->checkText($description);

        $isViolated = $titleCheck['is_violated'] || $descriptionCheck['is_violated'];
        $violations = array_merge($titleCheck['violations'], $descriptionCheck['violations']);
        $reason = $isViolated ? ($titleCheck['reason'] . ' ' . $descriptionCheck['reason']) : '';

        return [
            'is_violated' => $isViolated,
            'reason' => trim($reason),
            'violations' => $violations,
        ];
    }
}

