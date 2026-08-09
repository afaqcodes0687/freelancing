<?php

namespace Modules\Chat\Services;

class ProfanityFilterService
{
    /**
     * Checks if a given message contains any profanity/bad words.
     *
     * @param string|null $message
     * @return bool
     */
    public static function containsBadWords(?string $message): bool
    {
        if (empty($message)) {
            return false;
        }

        $badWords = [];

        // 0. Default fallback list
        $defaultBadWords = [
            'sex', 'porn', 'fuck', 'shit', 'bitch', 'asshole', 'dick', 'pussy', 'cunt', 'slut'
        ];
        
        $badWords = array_merge($badWords, $defaultBadWords);

        // 1. Check SecurityManage module dictionary (if available)
        if (moduleExists('SecurityManage')) {
            $restrictedWords = \Modules\SecurityManage\Entities\Word::where('status', 'active')->pluck('word')->toArray();
            if (!empty($restrictedWords)) {
                $badWords = array_merge($badWords, $restrictedWords);
            }
        }

        // 2. Check Custom Site Settings dictionary
        $customBadWords = get_static_option('profanity_words');
        if (!empty($customBadWords)) {
            $customWords = array_map('trim', explode(',', $customBadWords));
            $badWords = array_merge($badWords, $customWords);
        }

        // 3. No bad words configured at all
        if (empty($badWords)) {
            return false;
        }

        $message = strtolower($message);

        // 4. Check for any matches
        foreach ($badWords as $word) {
            $word = strtolower(trim($word));
            if (!empty($word) && strpos($message, $word) !== false) {
                return true;
            }
        }

        return false;
    }
}
