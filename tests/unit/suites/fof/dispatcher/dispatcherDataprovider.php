<?php

class DispatcherDataprovider
{
    public static function getTestGetTask()
    {
        $message = 'Incorrect task';

        // Should we test for ids on other cases, too?
        $data[] = array(new FOFInput(array('ids' => array(999))), 'foobar' , true,  'GET' 	 , 'read'  , $message);
        $data[] = array(new FOFInput(array('ids' => array(999))), 'foobar' , false,  'GET' 	 , 'edit'  , $message);
        $data[] = array(new FOFInput(array('id' => 999)), 'foobar' , true,  'GET' 	 , 'read'  , $message);
        $data[] = array(new FOFInput(array('id' => 999)), 'foobar' , false, 'GET' 	 , 'edit'  , $message);
        $data[] = array(new FOFInput(array())           , 'foobar' , true,  'GET'  	 , 'add'   , $message);
        $data[] = array(new FOFInput(array('id' => 999)), 'foobar' , true,  'POST'	 , 'save'  , $message);
        $data[] = array(new FOFInput(array())           , 'foobar' , true,  'POST'	 , 'edit'  , $message);
        $data[] = array(new FOFInput(array('id' => 999)), 'foobar' , true,  'PUT' 	 , 'save'  , $message);
        $data[] = array(new FOFInput(array())           , 'foobar' , true,  'PUT' 	 , 'edit'  , $message);
        $data[] = array(new FOFInput(array('id' => 999)), 'foobar' , true,  'DELETE' , 'delete'  , $message);
        $data[] = array(new FOFInput(array())           , 'foobar' , true,  'DELETE' , 'edit'  , $message);
        $data[] = array(new FOFInput(array('id' => 999)), 'foobars', true,  'GET' 	 , 'browse', $message);
        $data[] = array(new FOFInput(array())           , 'foobars', true,  'GET' 	 , 'browse', $message);
        $data[] = array(new FOFInput(array('id' => 999)), 'foobars', true,  'POST'	 , 'save'  , $message);
        $data[] = array(new FOFInput(array())           , 'foobars', true,  'POST'	 , 'browse', $message);

        return $data;
    }

    public static function getTestTransparentAuthentication()
    {
        // User is already logged in
        $data[] = array(
            array(
                'guest'  => false,
                'format' => 'html'
            ),
            array(
                'login' => false
            )
        );

        // Wrong format
        $data[] = array(
            array(
                'guest'  => false,
                'format' => 'wrong'
            ),
            array(
                'login' => false
            )
        );

        // HTTPBasicAuth_Plaintext
        $data[] = array(
            array(
                'guest'  => true,
                'format' => 'raw',
                'server' => array(
                    'PHP_AUTH_USER' => 'foftest',
                    'PHP_AUTH_PW'   => 'dummy'
                )
            ),
            array(
                'login' => true
            )
        );

        // QueryString_Plaintext -- OK
        $data[] = array(
            array(
                'guest'  => true,
                'format' => 'raw',
                'input'  => array(
                    '_fofauthentication' => json_encode(array(
                        'username' => 'foftest',
                        'password' => 'dummy'
                    ))
                )
            ),
            array(
                'login' => true
            )
        );

        // QueryString_Plaintext -- Wrong content
        $data[] = array(
            array(
                'guest'  => true,
                'format' => 'raw',
                'input'  => array(
                    '_fofauthentication' => json_encode('wrong content')
                )
            ),
            array(
                'login' => false
            )
        );

        // QueryString_Plaintext -- Missing username/password
        $data[] = array(
            array(
                'guest'  => true,
                'format' => 'raw',
                'input'  => array(
                    '_fofauthentication' => json_encode(array('foo', 'bar'))
                )
            ),
            array(
                'login' => false
            )
        );

        // SplitQueryString_Plaintext -- OK
        $data[] = array(
            array(
                'guest'  => true,
                'format' => 'raw',
                'input'  => array(
                    '_fofauthentication_username' => 'foftest',
                    '_fofauthentication_password' => 'dummy'
                )
            ),
            array(
                'login' => true
            )
        );

        // SplitQueryString_Plaintext -- OK (empty passwords are accepted)
        $data[] = array(
            array(
                'guest'  => true,
                'format' => 'raw',
                'input'  => array(
                    '_fofauthentication_username' => 'foftest',
                )
            ),
            array(
                'login' => true
            )
        );

        // SplitQueryString_Plaintext -- NOT OK (empty usernames are NOT accepted)
        $data[] = array(
            array(
                'guest'  => true,
                'format' => 'raw',
                'input'  => array(
                    '_fofauthentication_username' => '',
                    '_fofauthentication_password' => 'dummy'
                )
            ),
            array(
                'login' => false
            )
        );

        // HTTPBasicAuth_TOTP
        $encrypt = new FOFEncryptBase32;
        $key     = $encrypt->encode('FOF rocks!');
        $plain   = json_encode(array('username' => 'foftest', 'password' => 'dummy'));

        $totp      = new FOFEncryptTotp(6);
        $otp       = $totp->getCode($key);
        $cryptoKey = hash('sha256', $key.$otp);

        $aes       = new FOFEncryptAes($cryptoKey);
        $encrypted = $aes->encryptString($plain);

        $data[] = array(
            array(
                'guest'   => true,
                'format'  => 'raw',
                'authKey' => $key,
                'server'  => array(
                    'PHP_AUTH_USER' => '_fof_auth',
                    'PHP_AUTH_PW'   => $encrypted
                )
            ),
            array(
                'login' => true
            )
        );

        // QueryString_TOTP
        $encrypt = new FOFEncryptBase32;
        $key     = $encrypt->encode('FOF rocks!');
        $plain   = json_encode(array('username' => 'foftest', 'password' => 'dummy'));

        $totp      = new FOFEncryptTotp(6);
        $otp       = $totp->getCode($key);
        $cryptoKey = hash('sha256', $key.$otp);

        $aes       = new FOFEncryptAes($cryptoKey);
        $encrypted = $aes->encryptString($plain);

        $data[] = array(
            array(
                'guest'   => true,
                'format'  => 'raw',
                'authKey' => $key,
                'input'  => array(
                    '_fofauthentication'   => $encrypted
                )
            ),
            array(
                'login' => true
            )
        );

        return $data;
    }
}