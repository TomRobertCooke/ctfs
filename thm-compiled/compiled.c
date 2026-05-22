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
