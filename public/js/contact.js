grecaptcha.ready(function() {
    grecaptcha.execute('6LdhVPQUAAAAAMWgEIy0r15HgmtjcBt1XPo2isEP', {action: 'contact'}).then(function (token) {
        var recaptchaResponse = document.getElementById('recaptcha_response');
        recaptchaResponse.value = token;
    });
});