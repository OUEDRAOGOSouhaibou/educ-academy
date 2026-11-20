<?php
$cnf = 'C:/xampp/apache/conf/openssl.cnf';
if (file_exists($cnf)) {
    putenv("OPENSSL_CONF=$cnf");
    $_ENV['OPENSSL_CONF'] = $cnf;
}

require __DIR__ . '/../vendor/autoload.php';

// Si Jose ECKey a besoin de la variable d'environnement avant son chargement :
$res = openssl_pkey_new([
    'config' => file_exists($cnf) ? $cnf : null,
    'curve_name' => 'prime256v1',
    'private_key_type' => OPENSSL_KEYTYPE_EC
]);

if ($res) {
    openssl_pkey_export($res, $privateKeyPem, null, ['config' => $cnf]);
    $details = openssl_pkey_get_details($res);
    
    // x et y sont les coordonnées 32 octets de la clé publique
    $x = $details['ec']['x'];
    $y = $details['ec']['y'];
    $d = $details['ec']['d']; // clé privée 32 octets

    // Clé publique non compressée : 0x04 + x + y
    $publicKeyBytes = "\x04" . $x . $y;

    function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    $publicKey = base64UrlEncode($publicKeyBytes);
    $privateKey = base64UrlEncode($d);

    echo "VAPID_PUBLIC_KEY=" . $publicKey . "\n";
    echo "VAPID_PRIVATE_KEY=" . $privateKey . "\n";
} else {
    echo "Échec\n";
}
