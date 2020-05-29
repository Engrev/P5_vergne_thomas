# P5_vergne_thomas [![Codacy Badge](https://app.codacy.com/project/badge/Grade/71a9b4201ed14d6097ae606f2e40ed29)](https://www.codacy.com/manual/Engrev/P5_vergne_thomas?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Engrev/P5_vergne_thomas&amp;utm_campaign=Badge_Grade)

Require :
* Development environment
* Apache server 2.4
* PHP >= 7.2
* MySQL 5.7

Get started :
* Clone the repository with : _git clone https://github.com/Engrev/P5_vergne_thomas.git_.
* Make a _composer install_.
* Modify _PATH_, _COOKIE_DOMAIN_ and _COOKIE_PATH_ variables in _core/defines.php_ file according to your environment.
* Modify constants in _core/Database.php_ file according to your environment.
* Create _mail.txt_ file in _core_ folder with on line :
1. SMTP server.
2. SMTP username.
3. SMTP password.
4. TCP port. And that's all !
* Modify _DOMAINE_NAME_ variable in _core/defines.php_ file. This is the domain name of the email address you will use.
* To have the text editor "Tinymce" in your language, follow this [link](https://www.tiny.cloud/get-tiny/language-packages/) and download your language.
  Then unzip the archive and place the _langs_ folder in the _vendor/tinymce/tinymce_ folder.
  Finally, modify "LANGUAGE" in the _public/js/tinymce.js_ file.
* Go to the root of the project on your browser.