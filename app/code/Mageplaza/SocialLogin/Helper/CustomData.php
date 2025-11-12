<?php

namespace Mageplaza\SocialLogin\Helper;

use Magento\Framework\Exception\LocalizedException;
use Google\Client as GoogleClient;

class CustomData
{
    /**
     * Verify and return Google user info from ID token
     *
     * @param string $idToken
     * @return array
     * @throws LocalizedException
     */
    public function getGoogleUserData($idToken)
    {
        // ✅ Step 1: Verify token using Google SDK if available
        if (class_exists(GoogleClient::class)) {
            $client = new GoogleClient(['client_id' => '1098501657559-po7hrdt3pgpo3vool2e5bt8i41lvu6ts.apps.googleusercontent.com']); // <-- Replace with your actual Client ID
            $payload = $client->verifyIdToken($idToken);

            if (!$payload) {
                throw new LocalizedException(__('Invalid or expired Google token.'));
            }

            return [
                'email'        => $payload['email'] ?? null,
                'email_verified' => $payload['email_verified'] ?? false,
                'given_name'   => $payload['given_name'] ?? '',
                'family_name'  => $payload['family_name'] ?? '',
                'name'         => $payload['name'] ?? '',
                'picture'      => $payload['picture'] ?? ''
            ];
        }

        // ✅ Step 2: Fallback to CURL request (if SDK is not installed)
        $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            throw new LocalizedException(__(
                'Failed to verify Google token. (%1)',
                $curlErr ?: 'HTTP ' . $httpCode
            ));
        }

        $data = json_decode($response, true);
        if (empty($data['email'])) {
            throw new LocalizedException(__('Invalid Google token or missing email.'));
        }

        // ✅ Step 3: Optional audience validation (recommended)
        if ($data['aud'] !== '1098501657559-po7hrdt3pgpo3vool2e5bt8i41lvu6ts.apps.googleusercontent.com') { // <-- Replace with your app’s client ID
            throw new LocalizedException(__('Token audience mismatch.'));
        }

        return $data;
    }
}
