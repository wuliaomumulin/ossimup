# Common build variables.

VER			 = 
OFFSET		 = 
CC           = g++ 
LD           = ld
LDSO	     = $(CC) -shared -nostartfiles
CFLAGS		 = -fPIC -shared $(OFFSET) -O2
LINKFLAGS    = -lpthread -lz
MKDIR        = mkdir -p
RM           = rm -f
CP           = cp -fp
ECHO         = echo
FIND         = find
MV           = mv -f
LN           = ln -s
AR           = ar -r
ARFLAGS      = rv
DEBUG        = 


LINKFLAGS = /work/lib/usbkey/libgm3000.1.0.so -ldl

OBJSMAIN    = main.cpp
MAIN    = libusbkey.so

# Complie all
all: $(MAIN)
$(MAIN): $(OBJSMAIN)
	$(RM) $(MAIN)
	$(CC) -shared -Wl,-soname,adder -o $(MAIN) -fPIC $(OBJSMAIN) $(LINKFLAGS)

clean:
	$(RM) $(MAIN)
	$(RM) *.o $(MAIN)

# Complie the source code 

%.o: %.c
	$(CC) -c -o $@ $? 
%.o: %.cpp
	$(CC) -c -o $@ $? 
