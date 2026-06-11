# Assignment

> We intercepted a communication between Cipher and some 3 associates: Rivest, Shamir and Adleman. We were only able to retrieve a file.
> 
> ORDER: Get the secret key from the recovered file.

<br><br><br>

```python

from Crypto.Util.number import *
from flag import FLAG

def primo(n):
    n += 2 if n & 1 else 1
    while not isPrime(n):
        n += 2
    return n

p = getPrime(1024)
q = primo(p)
n = p * q
e = 0x10001
d = inverse(e, (p-1) * (q-1))
c = pow(bytes_to_long(FLAG.encode()), e, n)
#c = 3591116664311986976882299385598135447435246460706500887241769555088416359682787844532414943573794993699976035504884662834956846849863199643104254423886040489307177240200877443325036469020737734735252009890203860703565467027494906178455257487560902599823364571072627673274663460167258994444999732164163413069705603918912918029341906731249618390560631294516460072060282096338188363218018310558256333502075481132593474784272529318141983016684762611853350058135420177436511646593703541994904632405891675848987355444490338162636360806437862679321612136147437578799696630631933277767263530526354532898655937702383789647510
#n = 15956250162063169819282947443743274370048643274416742655348817823973383829364700573954709256391245826513107784713930378963551647706777479778285473302665664446406061485616884195924631582130633137574953293367927991283669562895956699807156958071540818023122362163066253240925121801013767660074748021238790391454429710804497432783852601549399523002968004989537717283440868312648042676103745061431799927120153523260328285953425136675794192604406865878795209326998767174918642599709728617452705492122243853548109914399185369813289827342294084203933615645390728890698153490318636544474714700796569746488209438597446475170891

```

<br><br><br>

# Notes

We can see that this is involving prime numbers and the names suggest RSA

> getPrime(N, randfunc=None)
> 	 
> getPrime(N:int, randfunc:callable):long Return a random N-bit prime number.
> If randfunc is omitted, then Random.new().read is used.

> primo(n)
> 
> if n is odd, start there and find next odd number
> if n is even, return 1
> the only even prime number is 2, so guaranteed to be 2 and 1, otherwise it's the closest primes together

<br>

## Algorithm

p = random 1024 bit prime number
q = first odd non prime number after p, every even number returns 15
n = p * q
e = 0x10001 (decimal 17)

> inverse(u, v)
> inverse(u:long, v:long):long Return the inverse of u mod v.

d = inverse of 17 % ((p-1) * (q-1))

> pow(base, exponent[, modulus])
> returns base^exponent % modulus

> The encode() method encodes the string into UTF-8 by default

c = (bytesToLong(flag).encode())^e % n

This appears to just be a variation of the rsa algorithm
In rsa the public key parts are n and e
private key parts are n and d

> let M equal the message and c equal the ciphertext
> decrypt: m = c^d % n
> encrypt: c = m^e % n

to decrypt, we have to figure out the value of d 

we know the values of e, c, and n 
we'd need p and q to solve for d

if this was done correctly, d would be mathmatically ridiculous to solve for
primo function appears to be the vulnerability

primo guarantees that q is the very next prime right after p unless p is 2, and then it's 1
p and q are very close together

n = p * q
if we know that the primes are close together, this is apparently a known factorization method/weakness of RSA
if the primes aren't distinct enough, then it's breakable

> fermat's factorization method
> odd integer N = a^2 - b^2 = (a + b)(a - b)
> if neither factor is equal to one, this is a proper factorization of N

this is still really difficult if you just do the raw factorization to solve for it 

the method that we can try is guessing values of a, hoping a^2 - N = b^2

pseudocode to solve for p and q

```

FermatFactor(N): // N should be odd
    a ← ceiling(sqrt(N))
    b2 ← a*a - N
    repeat until b2 is a square:
        a ← a + 1
        b2 ← a*a - N 
     // equivalently: 
     // b2 ← b2 + 2*a + 1
     // a ← a + 1
    return a - sqrt(b2) // or a + sqrt(b2)

```

Now that I solved for p and q, i have to solve for d
phi = (p-1)(q-1)
d = e^-1 % phi

m = c^d % N

calculate byte length because m is such a large int 
convert m into bytes and decode to get the flag

```bash

$ ./decode.py
***{****_****_*****_******_**_****}

```

if n was even, i would have to factor out the 2s first, but the number they gave me was odd
