<?php

namespace App\Socialite\Two;

use Laravel\Socialite\Two\User;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Illuminate\Support\Facades\Log;
use App\Helpers\JWT;

class ClaveUnicaProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = ['read', 'client_id=12'];

    protected $scopeSeparator = '&';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        //return $this->buildAuthUrlFromBase('https://devidentidad.mitic.gov.py/identidad/rest/authorization', $state);
        return 'https://devidentidad.mitic.gov.py/login?clientId=12&scope=read&responseType=code&state='. $state;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://devidentidad.mitic.gov.py/rest/authentication';
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        return array_add(
            parent::getTokenFields($code), 'grant_type', 'authorization_code'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->post('https://devidentidad.mitic.gov.py/identidad/rest/authentication', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
		'Content-Type' => 'application/json'
            ],
	    'query' => [
        	    'client_id' => $this->clientId,
	            'client_secret' => $this->clientSecret,
	            'code' => $this->request->input('code'),
        	    'grant_type' => 'authorization_code',
	        ],
        ]);
	Log::info($response->getBody());
	$tks = explode('.', $response->getBody());
	$payload = JWT::urlsafeB64Decode($tks[1]);
	Log::info($payload);
        return json_decode($payload, true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
	Log::info($user);
        return (new User)->setRaw($user)->map([
            'id' => $user['sub'],
            'name' => $user['nombres'] . ' ' . $user['apellidos'],
            'first_name' => $user['nombres'],
            'last_name' => $user['apellidos'],
            'primer_apellido' => $user['apellidos'],
            'segundo_apellido' => $user['apellidos'],
            'run' => $user['sub'],
            'dv' => $user['sub'],
            'email' => isset($user['email']) ? $user['email'] : null,
            'phone' => isset($user['telefonoMovil']) ? $user['telefonoMovil'] : null
        ]);
    }
}
