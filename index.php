<?php
/*
== Bilgilendirmeler ==
Instagram meyBot v1
Bu bot Türk Hack Team ailesi adına Ercan Ulucan tarafından yazılmıştır.
Başlangıç olarak sadece anasayfa beğeni sistemi hazırlanmıştır.
Geliştirici arkadaşlar dilerse geliştirebilir herhangi bir hak talebinde bulunmuyorum.
Benimle iletişime geçmek için erjanulujan@gmail.com veya https://instagr.am/erjanulujan


== Bot Hakkında ==
$username : kullanıcı adınızı yazın.
$password : şifrenizi yazın.
$proxyAddress : proxy kullanmak isterseniz örneğin $proxyAddress = '1.1.1.1:0000' şeklinde kullanabilirsiniz (varsayılan null).
$maxLike : bu değer bekleme süresinden önce kaç gönderinin beğenileceğini ayarlamanıza yardımcı olur (varsayılan 10).
$sleepFirst : bu değer ise yukardaki değeri kaç saniyede bir çalıştırmak istediğinizi ayarlamanıza yardımcı olur (varsayılan 10).
$sleepAfter : bu değer ise $maxLike değerindeki beğeni işlemi tamamlanınca beklenecek süreyi ayarlamanıza yardımcı olur (varsayılan 20).
$sleepError : bu değer ise beğeni hatası aldığınızda kaç saniye beklenmesini istiyorsanız onu ayarlamanıza yardımcı olur (varsayılan 600).
$likedCount : bu değer null kalmalıdır, aşağıdaki for döngüsünde yapılan işlem sayacı için gerekli bir değerdir.

Bu bot instagramdaki anasayfanızın gönderilerini belirli süreyle ve belirli adet ile otomatikmen beğenmenizi sağlar.


== Kurulum Aşaması ==
İlk olarak aşağıdan ayarlar bölümünü kendinize göre düzenleyin.
Daha sonra ise bilgisayarınıza "Composer" kurmalısınız (Ayrıca XAMPP kurmanız gereklidir).
Daha sonra bu dosyanın bulunduğu klasaöre "LShift" tuşuna basılı tutarak sağ tıklayın.
"Komut penceresini burada aç" seçeneğini seçtikten sonra aşağıdaki komutu girin.

php index.php

Bu komutu girdikten sonra bot herşeyi kendi yapacaktır.

Kullanımınız için teşekkür eder, İyi hitler dilerim.
Saygılarımla Ercan Ulucan
*/
set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__.'/vendor/autoload.php';

// Ayarlar //
$username = '';
$password = '';
$proxyAddress = null;
$maxLike = 10;
$sleepFirst = 10;
$sleepAfter = 20;
$sleepError = 600;
$likedCount = null;
$debug = false;
$truncatedDebug = false;
$maxId = null;
// Ayarlar //

try {
	$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
    echo "\n>>> Instagram meyBot v1\n";
	echo "\n>>> Coded by erjanulujan\n";
    echo "\n>>> Instagram: erjanulujan\n\n";
	sleep(2);
    if(!$proxyAddress == null){
        echo "\n[>] Proxy Baglantisi Saglaniyor! \n\n";
        $proxy = $ig->setProxy($proxyAddress);
    }
    echo "\n[?] Giris Yapmayi Deniyor... \n\n";
	$login = $ig->login($username,$password);
    echo "\n[>] Giris Basarili! \n\n";
}catch(Exception $e){
	echo "\n[!] Giris Basarisiz Oldu! \n";
    echo "[!] Hatali Bilgi Girdiniz veya Hesabiniz Dogrulamaya Dustu! \n\n";
    return false;
}


do {
    $getMedia = $ig->timeline->getTimelineFeed($maxId);

    $mediaCount = count($getMedia->getFeedItems());
    echo "\n[!] Gonderi Cekmeyi Deniyor! \n\n";
    for($i = 0; $i< $mediaCount; $i++){
        $getData = $getMedia->getFeedItems()[$i]->getMediaOrAd();
        $getJson = json_decode($getData,true);
        //print_r($getJson);

        if(!$getJson['has_liked'] == "1"){  
            if(!$getJson['id'] == ""){
            $likeMedia = $ig->media->like($getJson['id']);
            $likeJson = json_decode($likeMedia,true);
            if($likeJson['status'] == "ok"){
            echo "[>] [".$getJson['user']['username']."] Kullanicinin Gonderisi Begenildi! \n";
            flush();
            $likedCount++;
        }else{
            echo "[>] Begeni Islemi Basarisiz Oldu, Bu Yuzden [".$sleepError."] Saniye Sonra Islem Yapilacak!\n";
            sleep($sleepError);
        }
        }else{
            //echo "[>] Medya ID bulunamadi! \n";
        }
        }else{
            //echo "[>] [".$getJson['user']['username']."] Kullanicisinin [".$getJson['id']."] idli medyasi zaten begenilmis! \n";
        }
    if($maxLike == $likedCount){
        echo "\n\n===============================================================\n";
        echo "[!] Toplam [".$likedCount."] Gonderi Begenildi! \n";
        echo "[!] Bu Yuzden [".$sleepAfter."] Saniye Sonra Islem Yapilacak!";
        echo "\n===============================================================\n\n";
        sleep($sleepAfter);
        $likedCount = null;
    }
    }
    $maxId = $getMedia->getNextMaxId();
    sleep($sleepFirst);
}while($maxId !== null);