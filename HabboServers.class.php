<?php 
class HabboServers
{
    private $timeout, $apiURL, $slug;

    public function __construct()
    {
        global $_CONFIG;

        $this->timeout = $_CONFIG['timeout'];
        $this->apiURL = $_CONFIG['apiURL'];
        $this->slug = $_CONFIG['slug'];

        $_SERVER['REMOTE_ADDR'] = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
    }

    public function hasVoted()
    {
        if (!$this->hasCookie()) {
            $api =  $this->apiURL . 'validate.php?ip=' . $_SERVER['REMOTE_ADDR'] . '&hotel=' . $this->slug;
            $req = $this->getcURL($api);

            if ($req == 1) {
                $this->setVoteCookie();
                return true;
            } else if ($req == 2) {
                if (isset($_COOKIE['vote_expire']))  setcookie('vote_expire', '');
                return false;
            } else {
                $this->setVoteCookie();
                return true;
            }
        }
        return true;
    }

    public function goVote()
    {
        header('Location: ' . $this->apiURL . 'vote/' . $this->slug);
        exit;
    }

    private function getcURL($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }


    private function setVoteCookie()
    {
        $resetTime = time() + 86400;
        setcookie('vote_expire', $resetTime, $resetTime);
    }

    private function hasCookie()
    {
        if (isset($_COOKIE['vote_expire'])) {

            if ($_COOKIE['vote_expire'] >= time()) {
                return true;
            } else {
                setcookie('vote_expire', '');
                return false;
            }
        }
        return false;
    }
}
?>