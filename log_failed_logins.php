<?php

class log_failed_logins extends rcube_plugin
{
    public function init()
    {
        $this->add_hook('login_failed', array($this, 'login_failed'));
    }

    public function login_failed(array $args)
    {
        $username = $args['user'] ?? '<unknown user>';
        $hostname = $args['host'] ?? '<unknown host>';

        openlog('roundcube-login', LOG_PID, LOG_USER);

        $cf     = new \CloudFlare\IpRewrite();
        $date   = gmdate('Y-m-d H:i:s');
        $ip     = $cf->isCloudFlare() ? $cf->getRewrittenIP() : ($_SERVER['REMOTE_ADDR'] ?? '-');
        $method = $_SERVER['REQUEST_METHOD']  ?? '-';
        $proto  = $_SERVER['SERVER_PROTOCOL'] ?? '-';
        $host   = $_SERVER['HTTP_HOST']       ?? '-';
        $uri    = $_SERVER['REQUEST_URI']     ?? '-';
        $ref    = $_SERVER['HTTP_REFERER']    ?? '-';
        $ua     = $_SERVER['HTTP_USER_AGENT'] ?? '-';

        $message = "{$ip} {$host} [{$date}+0000] \"{$method} {$uri} {$proto}\" \"{$ref}\" \"{$ua}\" \"{$username}\" \"{$hostname}\"";
        syslog(LOG_WARNING, $message);
    }
}
