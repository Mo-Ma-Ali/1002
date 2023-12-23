<?php

namespace App\Http\Middleware;

use Closure;
use DateTime;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $language = $request->header('Accept-Language');

        if ($language == 'ar') {
            $tr = new GoogleTranslate('en');

            // Translate each value in the request data
            $translatedData = array_map(function ($value) use ($tr) {
                return $tr->setSource("ar")->setTarget("en")->translate($value);
            }, $request->all());

            // Convert the expire_date to 'Y-m-d' format after translation
            if (isset($translatedData['expire_date'])) {
                $expireDate = DateTime::createFromFormat('d-m-Y', $translatedData['expire_date']);
                $translatedData['expire_date'] = $expireDate ? $expireDate->format('Y-m-d') : null;
            }
           // dd($translatedData);
            // Ensure $translatedData is an array before replacing
            if (is_array($translatedData)) {
                $request->replace($translatedData);
            } else {
                // Log or handle unexpected translation response
            }
        }

        $response = $next($request);

        if ($language == 'ar') {
            $content = $response->getContent();
            $translatedContent = $this->recursiveTranslate(json_decode($content, true), 'en', 'ar');
            $response->setContent(json_encode($translatedContent));
        }

        return $response;
    }

    protected function recursiveTranslate($data, $sourceLang, $targetLang)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->recursiveTranslate($value, $sourceLang, $targetLang);
            }
        } else {
            $tr = new GoogleTranslate($sourceLang);
            $data = $tr->setSource($sourceLang)->setTarget($targetLang)->translate($data);
        }

        return $data;
    }
}
