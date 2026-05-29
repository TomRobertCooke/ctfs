# [TryHackMe - Simple CTF](https://tryhackme.com/room/easyctf)
- Author: [Thomas Cooke](http://github.com/tomrobertcooke)

<br>

<br>

## Challenge description:

> **Difficulty:** Easy
> 
> **Points:** 300
> 
> **Time Estimate:** 45 minutes
> 
> **Assignment:** Own root
> 
> **Flag:** user.txt and root.txt

<br>

<br>

## Writeup:

<br>

I started off just visiting their homepage. It's just the default apache page. I didn't see any low hanging fruit right off the bat.

<img width="863" height="472" alt="homepage" src="https://github.com/user-attachments/assets/f22a9ea8-ccc5-477b-9e68-60d49ac458ed" />

<br>

<br>

<br>

Then I enumerated ports and services with `nmap`.

<img width="1280" height="760" alt="nmap" src="https://github.com/user-attachments/assets/13bbdf74-a2a7-4a6c-9221-69037120eb4b" />

This gave me enough information to answer the first two questions.

> 
> How many services are running under port 1000?
> 
> What is running on the higher port?
> 

<br>

<br>

<br>

I anonymously logged into `ftp` next to see what I could find.

<img width="1277" height="579" alt="ftp recon" src="https://github.com/user-attachments/assets/3627d525-fd9c-4222-a555-ca1e8e138a22" />

All I could really do was download this `ForMitch.txt` file with `get`.

<img width="1280" height="100" alt="formitch" src="https://github.com/user-attachments/assets/7ade5f9c-469c-4b38-b381-ffd4759325a8" />

There's nothing actionable that I could get from this, but knowing that Mitch has a weak password is helpful regardless.

<br>

<br>

<br>

After that dead end I just continued with enumeration and brute forced for directories and hidden files with `gobuster`.

<img width="1280" height="438" alt="gobuster" src="https://github.com/user-attachments/assets/4d427407-b683-440c-8957-ae5b288ed967" />

<br>

<br>

<br>

The next obvious thing to do was visit `/simple`.

<img width="1194" height="531" alt="simple" src="https://github.com/user-attachments/assets/a3de366a-6015-4180-9352-66ff501178af" />

I've never heard of `CMS Made Simple` before, but I was able to find the version number at the bottom of the page.

<img width="1161" height="336" alt="simple version" src="https://github.com/user-attachments/assets/629ad8fe-151b-4cf5-a0bf-9e7be40cbe0a" />

<br>

<br>

<br>

Because the next question asked for a CVE, I went to [Exploit-Database](https://www.exploit-db.com/) to see what I could find.

<img width="1193" height="505" alt="db search" src="https://github.com/user-attachments/assets/5f66e540-3699-4e24-940a-ea72305a4890" />

<img width="1196" height="489" alt="cve page" src="https://github.com/user-attachments/assets/4163b900-a4aa-43a3-8ff1-a4d8c5fd9308" />

This search gave me CVE corresponding to our version of `CMS Made Simple` and gave me enough information to answer the next two questions.

> 
> What's the CVE you're using against the application?
> 
> To what kind of vulnerability is the application vulnerable?
> 

<br>

<br>

<br>

The next step was to run the exploit. It was able to brute force usernames and password hashes. As an added bonus it even had a password cracker when combined with a wordlist. I decided to run that as well because of Mitch's weak password.

<img width="1280" height="97" alt="exploit" src="https://github.com/user-attachments/assets/302a7b42-0ebc-49ce-aaad-6982871719f3" />

<img width="1280" height="161" alt="cracked" src="https://github.com/user-attachments/assets/112800f6-035b-4384-a078-cbe8d5180a2e" />

We got the password! Thanks to the nmap scan as well, we can use `ssh` to log into Mitch's account and answer the next 2 questions.

> 
> What's the password?
> 
> Where can you login with the details obtained?
> 

<br>

<br>

<br>

The next step was to `ssh` into Mitch's account.

<img width="1280" height="380" alt="mitch login" src="https://github.com/user-attachments/assets/3ef05278-e595-424f-8670-cd0391e823e8" />

After just checking what was in the current working directory with `ls`, the `user.txt` flag was right there, which answers the next question.

> 
> What's the user flag?
> 

I was able to check for the other user in the home directory to answer the next question as well.

<img width="553" height="121" alt="other user home" src="https://github.com/user-attachments/assets/124bcb75-6fa7-4bbe-b537-5566c55490f1" />

> 
> Is there any other user in the home directory? What's its name?
> 

<br>

<br>

<br>

Before looking into anything else, I checked what Mitch could run with `sudo`.

<img width="497" height="58" alt="sudo -l" src="https://github.com/user-attachments/assets/0ce5982e-a6ef-44ed-8673-4bdacd071ce8" />

I knew right off the bat that `vim` can spawn shells and is completely unsafe to have `sudo` privileges. So this allowed me to answer the second to last question.

> 
> What can you leverage to spawn a privileged shell?
> 

<br>

<br>

<br>

<img width="399" height="80" alt="vim shell" src="https://github.com/user-attachments/assets/dcf7fe01-3b96-4eac-97a1-093baae0368a" />

Now that I was in a root shell, the only thing left to do was look for the root flag and answer the last question.

<img width="424" height="101" alt="root flag" src="https://github.com/user-attachments/assets/8cab8f1d-d6f2-4f49-b4f2-32e2a41f1f26" />

> 
> What's the root flag?
> 