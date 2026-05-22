# [TryHackMe - Compiled](https://tryhackme.com/room/compiled)
- Author: [Thomas Cooke](http://github.com/tomrobertcooke)

<br>

## Challenge description:

> **Difficulty:** Easy
> 
> **Points:** 30
> 
> **Time Estimate:** 5 minutes
> 
> **Assignment:** Given the executable [Compiled-1688545393558.Compiled](https://github.com/TomRobertCooke/CTFs/blob/master/thm-compiled/Compiled-1688545393558.Compiled), I've been tasked with finding the password.
> 
> **Flag:** What is the password?

<br>

## Compilation hint:

If you wish to run [compiled.c](https://github.com/TomRobertCooke/CTFs/blob/master/thm-compiled/compiled.c) and test it yourself, I'd recommend using `gcc -fpermissive compile.c`. On my system, I had issues getting `__isoc99_scanf` to compile without using `-fpermissive` to reduce the error to a warning. There's nothing to be concerned about in this case, the linker should be mapping `scanf` to `__isoc99_scanf` in the first place (assuming you're compliant with isoc99).

<br>

## Writeup:

The first step that I could easily do here is execute the binary on a virtual machine to see what it does.

<img width="446" height="91" alt="first execute" src="https://github.com/user-attachments/assets/1f5b1579-4dcb-4838-b81d-935fee6f68e3" />

All it seemed to do is ask for the password, which I didn't have, so I can't go much further with running the binary until I figured that out.

<br>

<br>

<br>

The next step I took was to run `strings` on it to see what I could figure out about the binary. There wasn't much point in using `grep` or something similar to filter as the output was relatively short.

<img width="568" height="608" alt="strings" src="https://github.com/user-attachments/assets/4fc5b7c3-59b7-42da-8c37-86ed697a7c92" />

Most of this isn't very useful to me, but it's good to know that file is a 64 bit linux binary. The real meat and potatoes, however, would be the section starting with the two lines that look a lot like "StringsIsForNoobs". Right under it we can see the string that was outputted prompting me for the password ("Password: "). Following that appears to be a formatted string "DoYouEven%sCTF" which looks important. That was then followed by the two strings "\_\_dso\_handle" and "\_init". It was unclear what these two strings would mean, but they were both followed by the "Correct!" output and the "Try Again!" output that I received when I got the password wrong, so it was possible that they could be related.

<br>

<br>

<br>

During this process I also learned about the *"magic byte"* of a file. This refers to the hexadecimal signature in the first byte of a file indicating its type (e.g. a PDF file would start with `25 50 44 46 2D` or "%PDF-"). Like our executable here, any file can have its extension changed, but this signature should remain the same. I was able to use `xxd -l 8` on this binary to get a hexdump of the first byte, which indicated that this was an ELF executable.

<img width="557" height="81" alt="signature" src="https://github.com/user-attachments/assets/ab0d15a2-a5f4-4c4e-a3bf-e8c9005dd490" />

<br>

<br>

<br>

At this point it was time to open the executable in a decompiler of some sort. I decided to use [Ghidra](https://github.com/nationalsecurityagency/ghidra). After Ghidra did its automatic analysis of the binary, it was able to approximate the C code for the `main` function.

<img width="581" height="353" alt="ghidra" src="https://github.com/user-attachments/assets/fe39d585-0dc1-4ab9-a2dd-5f11d8f90d4a" />

It looks like the strings "\_\_dso\_handle" and "\_init" are more important than they initially appeared. The code prompts us for the password as expected, and then reads our input with `__isoc99_scanf` using the format `"DoYouEven%sCTF"`, and putting our input into `local_28`. The logic afterward seems to check if our inputted string is exactly equal to `"__dso_handle"` and immediately prints `"Try Again!"`. The more important section, though, is where it checks if the input string is equal to `"_init"` and prints `"Correct!"` if so.

<br>

<br>

<br>

The very first thing I did after this was input "DoYouEven_initCTF" into the binary, and this did not work. So, the behavior of `"__isoc99_scanf"` wasn't exactly how I expected initially. I decided to export Ghidra's code into [compiled.c](https://github.com/TomRobertCooke/CTFs/blob/master/thm-compiled/compiled.c) to test it further.

``` C
#include <stdio.h>
#include <string.h>

int main(void) {
  int iVar1;
  char local_28[32];

  printf("Password: ");
  __isoc99_scanf("DoYouEven%sCTF", local_28);
  printf("scanf: local_28: %s\n", local_28);
  iVar1 = strcmp(local_28, "__dso_handle");
  printf("strcmp __dso_handle: %d\n", iVar1);
  iVar1 = strcmp(local_28, "_init");
  printf("strcmp _init: %d", iVar1);
  return 0;
}
```

<br>

<br>

<br>

The goal here was to figure out why "DoYouEven_initCTF" didn't work properly, and find something that returned `0` for `strcmp(local_28, "_init")`.

<img width="499" height="274" alt="c testing" src="https://github.com/user-attachments/assets/a4fa1382-70b4-4153-b56b-7a90b8355a4e" />

I can't show the exact value that worked here as per TryHackMe's rules, but it wasn't too difficult at all, and this testing revealed how to get the proper password!

<img width="473" height="98" alt="flag blurred" src="https://github.com/user-attachments/assets/67adc1c2-45ec-4754-8c6f-22abd7907462" />

