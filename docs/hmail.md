#### Seting up hMail

> You do not need to setup hMail if you are going to test it directly on your own web hosting provider, but **DO NOT** forget to change SMTP credentials in `config/server.php` with prescribed default configurations


If you are running th **API** in your machine *(Which you likely do)* in ***windows*** , you need to setup your own **SMTP server** first. **Without hMail** or other similar SMTP server, transactions like **inviting, sending proposals, etc... will not work** and you will not be able to fully utilize BMS advanced features.       

To set up hMail Server, please [download](https://www.hmailserver.com/download) the latest version on its [official page](https://www.hmailserver.com/download) and follow [this instruction](http://peterkellner.net/2012/03/11/how-to-setup-your-own-pop3imap-email-server-for-local-development-testing/)

> Use mail.local domain and no-reply@mail.local account in your SMTP server   
