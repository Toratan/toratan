<?php
namespace modules\authModule\models;
(new \vendor\gAPI\googleAPIInitializer)->Execute();
/**
* The modules\authModule\models\gAuth
* @by Zinux Generator <b.g.dariush@gmail.com>
*/
class gAuth
{
    const SESSION_LABEL = "google_access_token";
    /**
     * The google client instance
     * @var \Google_Client
     */
    protected $client;
    /**
     * The google service Oauth2 instance
     * @var \Google_Service_Oauth2
     */
    protected $objOAuthService;
    public function __construct()
    {

        //Create Client Request to access Google API
        $this->client = new \Google_Client();
        $this->client->setApplicationName("Toratan");
        $this->client->setClientId(\zinux\kernel\application\config::GetConfig('auth.google.client_id'));
        $this->client->setClientSecret(\zinux\kernel\application\config::GetConfig('auth.google.client_secret'));
        $this->client->setRedirectUri(\zinux\kernel\application\config::GetConfig('auth.google.redirect_uri'));
        $this->client->addScope("https://www.googleapis.com/auth/plus.me");
        $this->client->addScope("https://www.googleapis.com/auth/userinfo.email");
        $this->client->addScope("https://www.googleapis.com/auth/userinfo.profile");
        
        $this->objOAuthService = new \Google_Service_Oauth2($this->client);
    }
    
    public function logout() { $this->client->revokeToken(); }
    public function authenticate($auth_code){
          $this->client->authenticate($auth_code);
          $_SESSION[self::SESSION_LABEL] = $this->client->getAccessToken();
    }
    public function getInfo() {
          $userData = $this->objOAuthService->userinfo->get();
          $_SESSION[self::SESSION_LABEL] = $this->client->getAccessToken();
          return empty($userData) ? NULL : $userData;
    }
    public function getAuthURI() { return $this->client->createAuthUrl(); }
}