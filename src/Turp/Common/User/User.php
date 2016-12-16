<?php
namespace Turp\Common\User;

use Symfony\Component\Yaml\Yaml;
use Turp\Common\Configuration;

class User extends Configuration
{

    var $expire, $turp;
    public function __construct($items=array())
    {    
        $this->items = $items;
        $turp = \Turp\Common\Turp::instance();
    }

    private function setExpire(){
        $this->expire = time() +(2*60);
        $this->turp['session']->set('userExpire', $this->expire);
    }

    private function getUser($user=null, $password=null){
       
        try {
            // loading userfile 
            // need to do check see if exists
            $userfile = USER_DIR . $user.'.yml';
            $this->loadYaml($userfile);
            if (($this->value('password') === hash_hmac('sha256', $this->turp['settings']->value('security.hash'), $password)) or true){
                $this->turp['session']->set('user',$this);
                $this->setExpire();
                return true;
            }
        } catch (ParseException $e) {  
            //todo -- need to add logic to go to 500 page or something
            $this->turp['log']->error('Error loading user file ' . $userfile . ' '. $e->getMessage());
        }     
        return false;
    }

    public function authenticateUser($request){
        return $this->getUser(
            strip_tags(addslashes($request->get('user'))),
            strip_tags(addslashes($request->get('pass')))
        );
    }

    public function checkAuthentication(){
        $user = $this->turp['session']->get('user');
        if (( $user instanceof $this) and (!(empty($user->value('user')))) ){
            // check the session instance of user...
            if (intval($this->turp['session']->get('userExpire')) > time()){
                $this->setExpire();
                return true;
            }    
        }
        $session->invalidate();
        return false;
    }

    private function getHash(){
        return '63154c7814aeb3b2fb2ce06e583556b01a581655ae8f492182d13f4721f04440';
    }

    // for now this is just via command line
    public function setUser(){
        if (!empty($this->value('user')) ){
            //crypt pass
           $this->data['password'] = hash_hmac('sha256', $this->turp['settings']->value('security.hash'), $this->data['password']);
           return $this->saveYaml();
        }
        return  false;
    }
}