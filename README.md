README
======

Update
-----------------
It's worth noting that I personally stopped using this engine since there is a much 
better way to just use some dynamic dns service like `no-ip` or `dyndns` and adding
a `cname` record to your domain pointing to the domain you selected at those services.


What is this?
-----------------

This is a [Dynamic DNS][1] engine for [Dreamhost][2] hosting provider users that
want to set up a domain or subdomain to point to their home/work PC with dynamic IP.
Please note that this is not truly Dynamic DNS as DNS refresh time
on Dreamhost at the time of writing was 4 hours. So any changes to DNS
might take almost twice that time to propagate.


Installation
-----------------

Installation procedure consists of 2 steps:

* Installation of server part.
* Installation of client.


Installation of server part
-----------------

* First of all you need to place the files under `web` directory to some
place on your hosting which is under the directory to which one of your domains is
pointed so that you can access it from web browser
(http://www.yourdomain.com/ddns/ for example).
* Edit `index.php` and insert:
 * your Dreamhost API key which you can request in [your Control Panel][3] as
(`dreamhost_api_key` key). You **must** fill in this field.
 * your email address to which the update result will be mailed
(`email` key). If email will be left blank no notifications will be sent.
 * log file path (`log_file_path` key) to log everything that happens to a log.
It is recommended to place log file upper the domain document root folder so
that is isn't accessible from outside. If this field will be left blank no log
will be written. Log file must have unix write access (`chmod 666 ddns.log`).
 * Personal key (`personal_key` key). Your **personal unique** key which is used to
protect ddns update script against everybody's access. You must fill in this field.
Every attempt to call your url without a valid key will be reported to an email provided
and logged. You can use `random.php` to generate one. To do that either run in shell
`php -f random.php` or browse to the `http://your.domain/your_path/random.php`
and record the key for later use.
 * Domains (`ddns_domains` key) - an array of domains you wish to use. These must be
domains that are pointed to Dreamhost name servers. It still can have only one domain.


Installing client on Linux
-----------------

* You need to place a script from `linux` directory to some place on your
home/work linux PC.
* Make `ddns.sh` executable (`chmod +x ddns.sh`).
* Edit `ddns.sh` and insert:
 * Public url to access your script placed on Dreamhost into `PUBLIC_URL`
 * Your personal unique key generated earlier into `PERSONAL_KEY`
* Test the script by calling it directly.
* Edit `/etc/network/interfaces` and insert line

        post-up /etc/ddns/ddns.sh

 to the interface which connects the web.

 Example:

        auto dsl-provider
        iface dsl-provider inet ppp
               pre-up /sbin/ifconfig eth1 up # line maintained by pppoeconf
               provider dsl-provider
               post-up /etc/ddns/ddns.sh
* test everything with reconnecting to your provider.


Installing client on Windows
-----------------

* You need to place a script from `windows` directory to some place on your
home/work windows PC.
* Obtain [`wget` for Windows][5] and place it near the `ddns.bat` in the
`windows` directory
* Edit `ddns.bat` and insert (**no spaces and quotes needed**):
 * Public url to access your script placed on Dreamhost into `PUBLIC_URL`
 * Your personal unique key generated earlier into `PERSONAL_KEY`
 * You can change the log file location in `LOG_PATH`
* Test the script by calling it directly.
* [Create a task][6] to launch the script `ddns.bat` on startup or logon.
* Test everything by loggin on/off or rebooting.


License
-----------------

The work is provided as is for free without any support guarantee under the [Creative Commons CC-BY-SA][4] license.


Author
-----------------

The work was made by Oleg Stepura. If you have questions feel free to contact me at
github [-at-] oleg.stepura.com

[1]: http://en.wikipedia.org/wiki/Dynamic_DNS
[2]: http://wiki.dreamhost.com/Dynamic_DNS
[3]: https://panel.dreamhost.com/?tree=home.api
[4]: http://creativecommons.org/licenses/by-sa/3.0/
[5]: http://gnuwin32.sourceforge.net/packages/wget.htm
[6]: http://www.sevenforums.com/tutorials/67503-task-create-run-program-startup-log.html
