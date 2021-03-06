<?php

namespace SocialiteProviders\Twitter;

use Laravel\Socialite\One\User;
use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Server as BaseServer;

class Server extends BaseServer
{
    /**
     * {@inheritdoc}
     */
    public function urlTemporaryCredentials()
    {
        return 'https://api.twitter.com/oauth/request_token';
    }

    /**
     * {@inheritdoc}
     */
    public function urlAuthorization()
    {
        return 'https://api.twitter.com/oauth/authorize';
    }

    /**
     * {@inheritdoc}
     */
    public function urlTokenCredentials()
    {
        return 'https://api.twitter.com/oauth/access_token';
    }

    /**
     * {@inheritdoc}
     */
    public function urlUserDetails()
    {
        return 'https://api.twitter.com/1.1/account/verify_credentials.json?include_email=true';
    }

    /**
     * {@inheritdoc}
     */
    public function userDetails($data, TokenCredentials $tokenCredentials)
    {
        $user = new User();
        $user->id = $data['id'];
        $user->nickname = $data['screen_name'];
        $user->name = $data['name'];
        $user->location = $data['location'];
        $user->description = $data['description'];
        $user->imageUrl = $data['profile_image_url'];
        $user->email = null;

        if (isset($data['email'])) {
            $user->email = $data['email'];
        }

        $used = ['id', 'screen_name', 'name', 'location', 'description', 'profile_image_url', 'email'];

        foreach ($data as $key => $value) {
            if (strpos($key, 'url') !== false) {
                if (!in_array($key, $used)) {
                    $used[] = $key;
                }

                $user->urls[$key] = $value;
            }
        }

        $user->extra = array_diff_key($data, array_flip($used));

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function userUid($data, TokenCredentials $tokenCredentials)
    {
        return $data['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function userEmail($data, TokenCredentials $tokenCredentials)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function userScreenName($data, TokenCredentials $tokenCredentials)
    {
        return $data['screen_name'];
    }
}
