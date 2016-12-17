<?php
namespace Turp\Common\User;

use Symfony\Component\Yaml\Yaml;
use Turp\Common\Configuration;

class User extends Configuration
{

    var $expire;
    public function __construct($items=array())
    {    
        $this->items = $items;
    }

    private function setExpire(){
        $this->expire = time() +(2*60);
        \Turp\Common\Turp::instance()['session']->set('userExpire', $this->expire);
    }

    private function getUser($user=null, $password=null){
        $turp = \Turp\Common\Turp::instance();
        try {
            // loading userfile 
            // need to do check see if exists
            $userfile = USER_DIR . $user.'.yml';
            $this->loadYaml($userfile);
            if (($this->value('password') === hash_hmac('sha256', $turp['settings']->value('security.hash'), $password)) ){
                $turp['session']->set('user',$this);
                $this->setExpire();
                return true;
            }
        } catch (ParseException $e) {  
            //todo -- need to add logic to go to 500 page or something
            $turp['log']->error('Error loading user file ' . $userfile . ' '. $e->getMessage());
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
        $turp = \Turp\Common\Turp::instance();
        $user = $turp['session']->get('user');
        if (( $user instanceof $this ) and (!(empty($user->value('user')))) ){
            // check the session instance of user...
            if (intval($turp['session']->get('userExpire')) > time()){
                $this->setExpire();
                return true;
            }    
        }
        $turp['session']->invalidate();
        return false;
    }

    private function getHash(){
        return '63154c7814aeb3b2fb2ce06e583556b01a581655ae8f492182d13f4721f04440';
    }
    
    

    // for now this is just via command line
    public function setUser(){
    
        if (!empty($this->value('user')) ){
            $this->file = USER_DIR . $this->value('user').'.yml';
            //crypt pass
           $this->set('password', hash_hmac('sha256', \Turp\Common\Turp::instance()['settings']->value('security.hash'), $this->value('password')));
           $this->saveYaml();
           return $this->value('user') . $this->value('password') . $this->file;
        }
        return  false;
    }
}