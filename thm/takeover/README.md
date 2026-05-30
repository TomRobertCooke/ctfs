# [TryHackMe - TakeOver](ctf-https://tryhackme.com/room/takeover)
- Author: [Thomas Cooke](http://github.com/tomrobertcooke)

<br>

<br>

## Challenge description:

> **Difficulty:** Easy
> 
> **Points:** 30
> 
> **Time Estimate:** 4 minutes
> 
> **Assignment:** The CEO of futurevera.thm has assigned me with figuring out what a group of blackhat hackers could takeover. It's a subdomain enumeration challenge primarily.
> 
> **Flag(s):** What is the value of the flag?

<br>

<br>

## Writeup

<br>

<br>

<br>

Even though this is a subdomain challenge, I did some basic enumeration for completion's sake. After adding `futurevera.thm` to my `/etc/hosts` file, I went to visit the site in a web browser. The homepage doesn't have too much going on here.

<img width="1191" height="711" alt="homepage" src="https://github.com/user-attachments/assets/2cc2a830-b02e-4f6d-b492-c0ad46d5a274" />

<br>

<br>

<br>

After that I did a quick nmap scan to see what services they might be running. Ports `80` and `443` are pretty much what you'd expect, and they're running `ssh` on the standard port as well. It's interesting that their certificate is expired. More certificate issues came up later, so it turned out to be important to keep that in mind.

image-<img width="1280" height="580" alt="nmap" src="https://github.com/user-attachments/assets/cdcb21db-ca4a-4499-821a-bf88996d2f9b" /

<br>

<br>

<br>

It seemed appropriate to brute force some directories with `gobuster` and see if any other information could be easily obtained.

image-<img width="1280" height="557" alt="dirs" src="https://github.com/user-attachments/assets/e2ea9548-b92a-4782-abad-d75058f0c556" />

I explored these, and while they were all viewable in a web browser, nothing really stuck out or looked particularly vulnerable at first glance

<br>

<br>

<br>

Then it was time to start looking for subdomains. I decided to use `wfuzz` to bruteforce them with a wordlist. It granted some useful results.

image-<img width="1280" height="450" alt="wfuzz" src="https://github.com/user-attachments/assets/8f392f1d-e254-44bf-b59d-077725121a02" />

The next natural step was to add `support.futurevera.thm` and `blog.futurevera.thm` to my `/etc/hosts` file and investigate them as well.

<br>

<br>

<br>

There wasn't much to gain from looking at `blog`. Its certificate was expired and self signed, but nothing else stood out there. When I visited the site without the certificate validation, there wasn't much there either. The `support` subdomain was much more helpful though.

<img width="1200" height="718" alt="advanced" src="https://github.com/user-attachments/assets/3c81a91a-5549-4f96-b89c-922ec06f5398" />

<img width="1200" height="718" alt="secret" src="https://github.com/user-attachments/assets/76448431-d44e-4e08-be68-26d128f64a90" />

Before even looking at the actual site, `support.futurevera.thm`'s certificate contained another hidden subdomain listed under a DNS `Subject Alternative Name` record. I added this new subdomain to my hosts as well and went to visit it.

<br>

<br>

<br>

When I visited this new hidden subdomain in my web browser, the error page and URL both contained the flag. Mission accomplished!

image-<img width="1200" height="718" alt="flag" src="https://github.com/user-attachments/assets/088ac639-1574-4997-a2a9-043fa0149413" />

<br>

<br>

<br>

## Lessons Learned and an Alternative Solution

I wasted a lot of time in this exercise fuzzing subdomains with alternative wordlists and bruteforcing directories of the two subdomains because I ignored the certificate warnings originally. I'd never personally investigated them before, so I learned how much information can be stored inside them, and I won't ignore them ever again.

This also took me down a bit of a rabbit hole researching how to investigate certificates more thoroughly, and I learned about using `openssl` in a command line as an alternative way to inspect the certificate and find the secret subdomain.

The full method was to use the `s_client` command to generate an SSL/TLS client with the `-connect <HOST>` and `-showcerts` options. Then I sent it null data to end the connection and piped the output to `x509` to make the output human readable.

```bash
$ openssl s_client -connect support.futurevera.thm:443 -showcerts </dev/null | openssl x509 -text -noout                                                             

depth=0 C = US, ST = Oregon, L = Portland, O = Futurevera, OU = Thm, CN = support.futurevera.thm
verify error:num=18:self-signed certificate
verify return:1
depth=0 C = US, ST = Oregon, L = Portland, O = Futurevera, OU = Thm, CN = support.futurevera.thm
...
        X509v3 extensions:
            X509v3 Key Usage:
                Key Encipherment, Data Encipherment
            X509v3 Extended Key Usage:
                TLS Web Server Authentication
            X509v3 Subject Alternative Name:
                DNS:*******************.support.futurevera.thm
...
```